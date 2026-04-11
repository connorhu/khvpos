<?php declare(strict_types=1);

namespace KHTools\VPos\Payum;

use KHTools\VPos\Keys\PrivateKey;
use KHTools\VPos\Models\Merchant;
use KHTools\VPos\Normalizers\AddressNormalizer;
use KHTools\VPos\Normalizers\CartItemNormalizer;
use KHTools\VPos\Normalizers\EnumNormalizer;
use KHTools\VPos\Normalizers\HttpErrorNormalizer;
use KHTools\VPos\Normalizers\RequestNormalizer;
use KHTools\VPos\Normalizers\ResponseNormalizer;
use KHTools\VPos\Payum\Action\AuthorizeAction;
use KHTools\VPos\Payum\Action\CancelAction;
use KHTools\VPos\Payum\Action\CaptureAction;
use KHTools\VPos\Payum\Action\ConvertPaymentAction;
use KHTools\VPos\Payum\Action\RefundAction;
use KHTools\VPos\Payum\Action\StatusAction;
use KHTools\VPos\Payum\Action\SyncAction;
use KHTools\VPos\SignatureProvider;
use KHTools\VPos\VPosClient;
use Payum\Core\Gateway;
use Payum\Core\GatewayFactoryInterface;
use Payum\Core\GatewayInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Client\ClientInterface;
use Symfony\Component\HttpClient\NativeHttpClient;
use Symfony\Component\HttpClient\Psr18Client;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AttributeLoader;
use Symfony\Component\Serializer\NameConverter\MetadataAwareNameConverter;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class KHVPosGatewayFactory implements GatewayFactoryInterface
{
    /**
     * Returns the config as-is; this factory does not use GatewayFactorySupport
     * so config defaults are applied directly in create().
     *
     * @param array<array-key, mixed> $config
     * @return array<array-key, mixed>
     */
    public function createConfig(array $config = []): array
    {
        return $config;
    }

    /**
     * Supported config keys: merchant_id (string, required), private_key_path (string, required),
     * private_key_passphrase (string|null), mips_public_key_path (string|null),
     * is_test (bool), api_version (string), http_client (ClientInterface).
     *
     * @param array<array-key, mixed> $config
     */
    public function create(array $config = []): GatewayInterface
    {
        if (!isset($config['merchant_id']) || $config['merchant_id'] === '') {
            throw new \InvalidArgumentException('The "merchant_id" config option is required.');
        }
        if (!isset($config['private_key_path']) || $config['private_key_path'] === '') {
            throw new \InvalidArgumentException('The "private_key_path" config option is required.');
        }

        $client = $this->buildVPosClient($config);

        $merchant = new Merchant();
        $merchant->merchantId = $config['merchant_id'];

        $gateway = new Gateway();
        $gateway->addAction(new ConvertPaymentAction());
        $gateway->addAction(new CaptureAction($client, $merchant));
        $gateway->addAction(new AuthorizeAction($client, $merchant));
        $gateway->addAction(new StatusAction());
        $gateway->addAction(new RefundAction($client, $merchant));
        $gateway->addAction(new CancelAction($client, $merchant));
        $gateway->addAction(new SyncAction($client, $merchant));

        return $gateway;
    }

    /**
     * @param array<string, mixed> $config
     */
    private function buildVPosClient(array $config): VPosClient
    {
        if (isset($config['http_client'])) {
            $psr18 = $config['http_client'];
        } else {
            if (!class_exists(NativeHttpClient::class)) {
                throw new \RuntimeException(
                    'Install symfony/http-client or pass an "http_client" (PSR-18) in the factory config.'
                );
            }
            $psr18 = new Psr18Client(new NativeHttpClient());
        }

        $classMetadataFactory = new ClassMetadataFactory(new AttributeLoader());
        $nameConverter = new MetadataAwareNameConverter($classMetadataFactory);
        $extractor = new PropertyInfoExtractor([], [new ReflectionExtractor()]);
        $objectNormalizer = new ObjectNormalizer($classMetadataFactory, $nameConverter, null, $extractor);

        $mipsPublicKeyPath = $config['mips_public_key_path'] ?? $this->getBundledMipsPublicKeyPath(
            (bool) ($config['is_test'] ?? false)
        );

        $signatureProvider = new SignatureProvider(
            [$config['merchant_id'] => new PrivateKey(
                $config['private_key_path'],
                $config['private_key_passphrase'] ?? null,
            )],
            $mipsPublicKeyPath,
        );

        $serializer = new Serializer(
            [
                new ResponseNormalizer($signatureProvider, $objectNormalizer),
                new RequestNormalizer($objectNormalizer),
                new CartItemNormalizer($objectNormalizer),
                new AddressNormalizer($objectNormalizer),
                new HttpErrorNormalizer(),
                new EnumNormalizer(),
                new DateTimeNormalizer(),
                $objectNormalizer,
            ],
            [new JsonEncoder()],
        );
        $objectNormalizer->setSerializer($serializer);

        $services = [
            'KHTools\VPos\VPosClient::getSignatureProvider' => $signatureProvider,
            'KHTools\VPos\VPosClient::getNormalizer'        => $serializer,
            'KHTools\VPos\VPosClient::getDenormalizer'      => $serializer,
            'KHTools\VPos\VPosClient::getSerializer'        => $serializer,
            'KHTools\VPos\VPosClient::getHttpClient'        => $psr18,
            'KHTools\VPos\VPosClient::getRequestFactory'    => $psr18,
            'KHTools\VPos\VPosClient::getStreamFactory'     => $psr18,
        ];

        $container = new class($services) implements ContainerInterface {
            /** @param array<string, mixed> $services */
            public function __construct(private readonly array $services) {}
            public function get(string $id): mixed { return $this->services[$id]; }
            public function has(string $id): bool { return isset($this->services[$id]); }
        };

        $vposClient = new VPosClient(
            $config['api_version'] ?? VPosClient::VERSION_REST_V1,
            (bool) ($config['is_test'] ?? false),
        );
        $vposClient->setContainer($container);

        return $vposClient;
    }

    private function getBundledMipsPublicKeyPath(bool $sandbox): string
    {
        return sprintf(
            '%s/../Bundle/Resources/keys/mips_pay%s.khpos.hu.pub',
            __DIR__,
            $sandbox ? '.sandbox' : '',
        );
    }
}
