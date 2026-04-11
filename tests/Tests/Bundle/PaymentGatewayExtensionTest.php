<?php declare(strict_types=1);

namespace KHTools\Tests\Bundle;

use KHTools\VPos\Bundle\DependencyInjection\PaymentGatewayExtension;
use KHTools\VPos\Bundle\Providers\MerchantProviderInterface;
use KHTools\VPos\SignatureProviderInterface;
use KHTools\VPos\VPosClient;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class PaymentGatewayExtensionTest extends TestCase
{
    private ContainerBuilder $container;
    private PaymentGatewayExtension $extension;

    protected function setUp(): void
    {
        $this->container = new ContainerBuilder();
        $this->extension = new PaymentGatewayExtension();
    }

    private function load(array $overrides = []): void
    {
        $this->extension->load([array_merge([
            'test' => true,
            'merchants' => [
                'default' => [
                    'currency' => 'HUF',
                    'merchant_id' => 'M123456789',
                    'private_key_path' => '/tmp/key.pem',
                    'private_key_passphrase' => '',
                ],
            ],
        ], $overrides)], $this->container);
    }

    public function testAllSixNormalizersAreRegisteredWithTag(): void
    {
        $this->load();

        $expectedNormalizers = [
            'khvpos.serializer.normalizer.address_normalizer',
            'khvpos.serializer.normalizer.cart_item_normalizer',
            'khvpos.serializer.normalizer.enum_normalizer',
            'khvpos.serializer.normalizer.http_error_normalizer',
            'khvpos.serializer.normalizer.request_normalizer',
            'khvpos.serializer.normalizer.response_normalizer',
        ];

        foreach ($expectedNormalizers as $id) {
            $this->assertTrue($this->container->hasDefinition($id), "Service '$id' not registered");
            $tags = $this->container->getDefinition($id)->getTags();
            $this->assertArrayHasKey('serializer.normalizer', $tags, "Service '$id' missing serializer.normalizer tag");
            $this->assertSame(-915, $tags['serializer.normalizer'][0]['priority'], "Service '$id' has wrong priority");
        }
    }

    public function testVPosClientIsRegisteredAndAliased(): void
    {
        $this->load();

        $this->assertTrue($this->container->hasDefinition('khvpos.vpos_client'));
        $this->assertTrue($this->container->hasAlias(VPosClient::class));
        $this->assertSame('khvpos.vpos_client', (string) $this->container->getAlias(VPosClient::class));
    }

    public function testSignatureProviderIsRegisteredLazyAndAliased(): void
    {
        $this->load();

        $this->assertTrue($this->container->hasDefinition('khvpos.signature_provider'));
        $this->assertTrue($this->container->getDefinition('khvpos.signature_provider')->isLazy());
        $this->assertTrue($this->container->hasAlias(SignatureProviderInterface::class));
    }

    public function testMerchantProviderIsRegisteredAndAliased(): void
    {
        $this->load();

        $this->assertTrue($this->container->hasDefinition('khvpos.merchant_provider'));
        $this->assertTrue($this->container->hasAlias(MerchantProviderInterface::class));
    }

    public function testSignatureProviderReceivesAddPrivateKeyCallsPerMerchant(): void
    {
        $this->extension->load([['test' => true, 'merchants' => [
            'merchant_a' => ['currency' => 'HUF', 'merchant_id' => 'M111', 'private_key_path' => '/a.pem', 'private_key_passphrase' => ''],
            'merchant_b' => ['currency' => 'EUR', 'merchant_id' => 'M222', 'private_key_path' => '/b.pem', 'private_key_passphrase' => 'secret'],
        ]]], $this->container);

        $calls = $this->container->getDefinition('khvpos.signature_provider')->getMethodCalls();
        $this->assertCount(2, $calls);
        $this->assertSame('addPrivateKey', $calls[0][0]);
        $this->assertSame(['M111', '/a.pem', ''], $calls[0][1]);
        $this->assertSame('addPrivateKey', $calls[1][0]);
        $this->assertSame(['M222', '/b.pem', 'secret'], $calls[1][1]);
    }

    public function testSignatureProviderUsesCustomMipsPublicKeyPath(): void
    {
        $this->extension->load([['test' => false, 'mips_public_key_path' => '/custom/key.pub', 'merchants' => [
            'default' => ['currency' => 'HUF', 'merchant_id' => 'M123', 'private_key_path' => '/tmp/key.pem', 'private_key_passphrase' => ''],
        ]]], $this->container);

        $args = $this->container->getDefinition('khvpos.signature_provider')->getArguments();
        $this->assertSame('/custom/key.pub', $args[1]);
    }

    public function testSignatureProviderUsesBundledProductionKeyWhenNotTestAndNoCustomPath(): void
    {
        $this->extension->load([['test' => false, 'merchants' => [
            'default' => ['currency' => 'HUF', 'merchant_id' => 'M123', 'private_key_path' => '/tmp/key.pem', 'private_key_passphrase' => ''],
        ]]], $this->container);

        $args = $this->container->getDefinition('khvpos.signature_provider')->getArguments();
        $this->assertStringEndsWith('mips_pay.khpos.hu.pub', $args[1]);
    }

    public function testAliasIsKhvpos(): void
    {
        $this->assertSame('khvpos', $this->extension->getAlias());
    }
}
