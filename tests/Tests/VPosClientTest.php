<?php declare(strict_types=1);

namespace KHTools\Tests;

use KHTools\VPos\Exceptions\ClientErrorException;
use KHTools\VPos\Keys\PrivateKey;
use KHTools\VPos\Models\Enums\Currency;
use KHTools\VPos\Models\Enums\HttpMethod;
use KHTools\VPos\Models\Merchant;
use KHTools\VPos\Normalizers\AddressNormalizer;
use KHTools\VPos\Normalizers\CartItemNormalizer;
use KHTools\VPos\Normalizers\EnumNormalizer;
use KHTools\VPos\Normalizers\HttpErrorNormalizer;
use KHTools\VPos\Normalizers\RequestNormalizer;
use KHTools\VPos\Normalizers\ResponseNormalizer;
use KHTools\VPos\Requests\PaymentInitRequest;
use KHTools\VPos\Requests\PaymentProcessRequest;
use KHTools\VPos\Requests\PaymentStatusRequest;
use KHTools\VPos\Responses\PaymentInitResponse;
use KHTools\VPos\Responses\PaymentStatusResponse;
use KHTools\VPos\SignatureProvider;
use KHTools\VPos\VPosClient;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Psr18Client;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AttributeLoader;
use Symfony\Component\Serializer\NameConverter\MetadataAwareNameConverter;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class VPosClientTest extends TestCase
{
    private const FIXTURES = __DIR__ . '/Fixtures';
    private const MERCHANT_ID = 'M123';

    private function merchant(): Merchant
    {
        $m = new Merchant();
        $m->merchantId = self::MERCHANT_ID;
        return $m;
    }

    /**
     * Builds a VPosClient with real Serializer + SignatureProvider and a mock HTTP layer.
     * Pass MockResponse instances for the mock HTTP layer.
     * Returns both the client and the MockHttpClient so tests can inspect sent requests.
     *
     * @return array{VPosClient, MockHttpClient}
     */
    private function buildClient(MockResponse ...$mockResponses): array
    {
        $mockHttpClient = new MockHttpClient($mockResponses);
        $psr18 = new Psr18Client($mockHttpClient);

        $classMetadataFactory = new ClassMetadataFactory(new AttributeLoader());
        $nameConverter = new MetadataAwareNameConverter($classMetadataFactory);
        $extractor = new PropertyInfoExtractor([], [new ReflectionExtractor()]);
        $objectNormalizer = new ObjectNormalizer($classMetadataFactory, $nameConverter, null, $extractor);

        $signatureProvider = new SignatureProvider(
            [self::MERCHANT_ID => new PrivateKey(self::FIXTURES . '/test1_private_key.pem')],
            self::FIXTURES . '/test1_public_key.pem',
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
            public function __construct(private array $services) {}
            public function get(string $id): mixed { return $this->services[$id]; }
            public function has(string $id): bool { return isset($this->services[$id]); }
        };

        $client = new VPosClient(VPosClient::VERSION_REST_V1);
        $client->setContainer($container);

        return [$client, $mockHttpClient];
    }

    public function testSendPostRequestDeserializesResponse(): void
    {
        // JSON does not include 'signature' — ResponseNormalizer skips verification when absent
        $responseJson = json_encode([
            'payId'         => 'abc123',
            'dttm'          => '20240101120000',
            'resultCode'    => 0,
            'resultMessage' => 'OK',
            'paymentStatus' => 1,
        ]);

        [$client] = $this->buildClient(new MockResponse($responseJson, ['http_code' => 200]));

        $request = new PaymentInitRequest();
        $request->setMerchant($this->merchant());
        $request->setOrderNumber('order001');
        $request->setTotalAmount(1000);
        $request->setCurrency(Currency::HUF);
        $request->setReturnUrl('https://example.com/return');
        $request->setReturnMethod(HttpMethod::Post);

        $response = $client->send($request);

        $this->assertInstanceOf(PaymentInitResponse::class, $response);
        $this->assertSame('abc123', $response->getPaymentId());
        $this->assertSame(0, $response->getResultCode());
        $this->assertSame('OK', $response->getResultMessage());
        $this->assertSame(1, $response->getPaymentStatus());
    }

    public function testSendGetRequestBuildsCorrectUrl(): void
    {
        $responseJson = json_encode([
            'payId'         => 'pay001',
            'dttm'          => '20240101120000',
            'resultCode'    => 0,
            'resultMessage' => 'OK',
            'paymentStatus' => 4,
        ]);

        $capturedResponse = new MockResponse($responseJson, ['http_code' => 200]);
        [$client] = $this->buildClient($capturedResponse);

        $request = new PaymentStatusRequest();
        $request->setMerchant($this->merchant());
        $request->setPaymentId('pay001');

        $client->send($request);

        $url = $capturedResponse->getRequestUrl();
        $this->assertStringContainsString('pay001', $url);
        $this->assertStringContainsString(self::MERCHANT_ID, $url);
        // Signature must be URL-encoded in GET path (no raw '+' sign)
        $signatureSegment = explode('/', $url);
        $rawSignature = array_pop($signatureSegment);
        $this->assertStringNotContainsString('+', $rawSignature);
    }

    public function testSendThrowsClientErrorExceptionOnHttp400(): void
    {
        $responseJson = json_encode([
            'resultCode'    => 400,
            'resultMessage' => 'Bad Request',
        ]);

        [$client] = $this->buildClient(new MockResponse($responseJson, ['http_code' => 400]));

        $request = new PaymentStatusRequest();
        $request->setMerchant($this->merchant());
        $request->setPaymentId('pay001');

        $this->expectException(ClientErrorException::class);
        $client->send($request);
    }

    public function testGetPaymentUrlContainsPayIdAndMerchantId(): void
    {
        [$client] = $this->buildClient();

        $request = new PaymentProcessRequest();
        $request->setMerchant($this->merchant());
        $request->setPaymentId('pay999');

        $url = $client->getPaymentUrlWithPaymentProcessRequest($request);

        $this->assertStringStartsWith('https://api.khpos.hu/api/v1.0/payment/process/', $url);
        $this->assertStringContainsString('pay999', $url);
        $this->assertStringContainsString(self::MERCHANT_ID, $url);
    }

    public function testGetPaymentUrlUsesSandboxWhenTestModeEnabled(): void
    {
        $classMetadataFactory = new ClassMetadataFactory(new AttributeLoader());
        $nameConverter = new MetadataAwareNameConverter($classMetadataFactory);
        $extractor = new PropertyInfoExtractor([], [new ReflectionExtractor()]);
        $objectNormalizer = new ObjectNormalizer($classMetadataFactory, $nameConverter, null, $extractor);

        $signatureProvider = new SignatureProvider(
            [self::MERCHANT_ID => new PrivateKey(self::FIXTURES . '/test1_private_key.pem')],
            self::FIXTURES . '/test1_public_key.pem',
        );

        $serializer = new Serializer(
            [new RequestNormalizer($objectNormalizer), new EnumNormalizer(), $objectNormalizer],
            [new JsonEncoder()],
        );

        $psr18 = new Psr18Client(new MockHttpClient());
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
            public function __construct(private array $services) {}
            public function get(string $id): mixed { return $this->services[$id]; }
            public function has(string $id): bool { return isset($this->services[$id]); }
        };

        $client = new VPosClient(VPosClient::VERSION_REST_V1, isTest: true);
        $client->setContainer($container);

        $request = new PaymentProcessRequest();
        $request->setMerchant($this->merchant());
        $request->setPaymentId('pay001');

        $url = $client->getPaymentUrlWithPaymentProcessRequest($request);

        $this->assertStringStartsWith('https://api.sandbox.khpos.hu/api/v1.0/payment/process/', $url);
    }

    public function testInitResponseWithArrayDeserializesPaymentStatusResponse(): void
    {
        [$client] = $this->buildClient();

        $data = [
            'payId'         => 'xyz789',
            'dttm'          => '20240101120000',
            'resultCode'    => 0,
            'resultMessage' => 'OK',
            'paymentStatus' => 4,
            'authCode'      => 'F7A23E',
        ];

        $response = $client->initResponseWithArray($data, PaymentStatusResponse::class);

        $this->assertInstanceOf(PaymentStatusResponse::class, $response);
        $this->assertSame('xyz789', $response->getPaymentId());
        $this->assertSame(0, $response->getResultCode());
        $this->assertSame(4, $response->getPaymentStatus());
        $this->assertSame('F7A23E', $response->getAuthorizationCode());
    }
}
