<?php declare(strict_types=1);

namespace KHTools\Tests\Bundle\Integration;

use KHTools\VPos\Bundle\Providers\MerchantProviderInterface;
use KHTools\VPos\SignatureProviderInterface;
use KHTools\VPos\VPosClient;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class BundleIntegrationTest extends KernelTestCase
{
    protected static function getKernelClass(): string
    {
        return TestKernel::class;
    }

    public function testVPosClientIsResolvableFromContainer(): void
    {
        self::bootKernel();
        $container = self::getContainer();

        $this->assertTrue($container->has(VPosClient::class));
        $this->assertInstanceOf(VPosClient::class, $container->get(VPosClient::class));
    }

    public function testSignatureProviderIsResolvableFromContainer(): void
    {
        self::bootKernel();
        $container = self::getContainer();

        $this->assertTrue($container->has(SignatureProviderInterface::class));
    }

    public function testMerchantProviderIsResolvableFromContainer(): void
    {
        self::bootKernel();
        $container = self::getContainer();

        $this->assertTrue($container->has(MerchantProviderInterface::class));
    }

    public function testAllSixNormalizersAreRegisteredInContainer(): void
    {
        self::bootKernel();
        $container = self::getContainer();

        $expectedNormalizers = [
            'khvpos.serializer.normalizer.address_normalizer',
            'khvpos.serializer.normalizer.cart_item_normalizer',
            'khvpos.serializer.normalizer.enum_normalizer',
            'khvpos.serializer.normalizer.http_error_normalizer',
            'khvpos.serializer.normalizer.request_normalizer',
            'khvpos.serializer.normalizer.response_normalizer',
        ];

        foreach ($expectedNormalizers as $id) {
            $this->assertTrue($container->has($id), "Service '$id' not found in container");
        }
    }
}
