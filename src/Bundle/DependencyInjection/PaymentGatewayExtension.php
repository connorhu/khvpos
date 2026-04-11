<?php declare(strict_types=1);

namespace KHTools\VPos\Bundle\DependencyInjection;

use KHTools\VPos\Bundle\Providers\MerchantProvider;
use KHTools\VPos\Bundle\Providers\MerchantProviderInterface;
use KHTools\VPos\Normalizers\AddressNormalizer;
use KHTools\VPos\Normalizers\CartItemNormalizer;
use KHTools\VPos\Normalizers\EnumNormalizer;
use KHTools\VPos\Normalizers\HttpErrorNormalizer;
use KHTools\VPos\Normalizers\RequestNormalizer;
use KHTools\VPos\Normalizers\ResponseNormalizer;
use KHTools\VPos\SignatureProvider;
use KHTools\VPos\SignatureProviderInterface;
use KHTools\VPos\VPosClient;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;

class PaymentGatewayExtension extends Extension
{
    private function getBundledMipsPublicKeyPath(bool $sandbox): string
    {
        return sprintf(
            '%s/Resources/keys/mips_pay%s.khpos.hu.pub',
            \dirname(__DIR__),
            $sandbox ? '.sandbox' : ''
        );
    }

    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        $container->setParameter('khvpos.client_provider.config', $config);

        $this->setupSerializer($config, $container);
        $this->setupSignatureProvider($config, $container);
        $this->setupVPosClient($config, $container);
        $this->setupMerchantProvider($config, $container);
    }

    /**
     * @param array<string, mixed> $configuration
     */
    protected function setupSerializer(array $configuration, ContainerBuilder $container): void
    {
        $container->register('khvpos.serializer.normalizer.address_normalizer', AddressNormalizer::class)
            ->addTag('serializer.normalizer', ['priority' => -915])
            ->setAutowired(true);

        $container->register('khvpos.serializer.normalizer.cart_item_normalizer', CartItemNormalizer::class)
            ->addTag('serializer.normalizer', ['priority' => -915])
            ->setAutowired(true);

        $container->register('khvpos.serializer.normalizer.enum_normalizer', EnumNormalizer::class)
            ->addTag('serializer.normalizer', ['priority' => -915]);

        $container->register('khvpos.serializer.normalizer.http_error_normalizer', HttpErrorNormalizer::class)
            ->addTag('serializer.normalizer', ['priority' => -915]);

        $container->register('khvpos.serializer.normalizer.request_normalizer', RequestNormalizer::class)
            ->addTag('serializer.normalizer', ['priority' => -915])
            ->setAutowired(true);

        $container->register('khvpos.serializer.normalizer.response_normalizer', ResponseNormalizer::class)
            ->addTag('serializer.normalizer', ['priority' => -915])
            ->setAutowired(true);
    }

    /**
     * @param array<string, mixed> $configuration
     */
    protected function setupVPosClient(array $configuration, ContainerBuilder $container): void
    {
        $container->register('khvpos.vpos_client', VPosClient::class)
            ->setArguments([
                $configuration['version'],
                $configuration['test'],
            ])
            ->setAutoconfigured(true)
            ->setAutowired(true);

        $container->setAlias(VPosClient::class, 'khvpos.vpos_client')->setPublic(true);
    }

    /**
     * @param array<string, mixed> $configuration
     */
    protected function setupSignatureProvider(array $configuration, ContainerBuilder $container): void
    {
        $mipsPublicKeyPath = $configuration['mips_public_key_path']
            ?? $this->getBundledMipsPublicKeyPath($configuration['test']);

        $definition = $container->register('khvpos.signature_provider', SignatureProvider::class)
            ->setArguments([[], $mipsPublicKeyPath])
            ->addTag('khvpos.signature_provider')
            ->setLazy(true);

        foreach ($configuration['merchants'] as $clientConfig) {
            $definition->addMethodCall('addPrivateKey', [
                $clientConfig['merchant_id'],
                $clientConfig['private_key_path'],
                $clientConfig['private_key_passphrase'],
            ]);
        }

        $container->setAlias(SignatureProviderInterface::class, 'khvpos.signature_provider')->setPublic(true);
    }

    /**
     * @param array<string, mixed> $configuration
     */
    protected function setupMerchantProvider(array $configuration, ContainerBuilder $container): void
    {
        $merchantProviderConfig = [];
        foreach ($configuration['merchants'] as $clientConfig) {
            $merchantProviderConfig[$clientConfig['currency']] = $clientConfig['merchant_id'];
        }

        $container->register('khvpos.merchant_provider', MerchantProvider::class)
            ->setArgument(0, $merchantProviderConfig);

        $container->setAlias(MerchantProviderInterface::class, 'khvpos.merchant_provider')->setPublic(true);
    }

    public function getAlias(): string
    {
        return 'khvpos';
    }
}
