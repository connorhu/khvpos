<?php declare(strict_types=1);

namespace KHTools\Tests\Bundle;

use KHTools\VPos\Bundle\DependencyInjection\Configuration;
use KHTools\VPos\VPosClient;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\Config\Definition\Processor;

class ConfigurationTest extends TestCase
{
    private Processor $processor;
    private Configuration $configuration;

    protected function setUp(): void
    {
        $this->processor = new Processor();
        $this->configuration = new Configuration();
    }

    private function process(array $config): array
    {
        return $this->processor->processConfiguration($this->configuration, [$config]);
    }

    public function testDefaultsAreApplied(): void
    {
        $config = $this->process([
            'merchants' => [
                'default' => [
                    'merchant_id' => 'M123456789',
                    'private_key_path' => '/tmp/key.pem',
                ],
            ],
        ]);

        $this->assertTrue($config['test']);
        $this->assertNull($config['mips_public_key_path']);
        $this->assertSame(VPosClient::VERSION_REST_V1, $config['version']);
    }

    public function testMerchantDefaultCurrencyIsHuf(): void
    {
        $config = $this->process([
            'merchants' => [
                'default' => [
                    'merchant_id' => 'M123456789',
                    'private_key_path' => '/tmp/key.pem',
                ],
            ],
        ]);

        $this->assertSame('HUF', $config['merchants']['default']['currency']);
    }

    public function testMerchantPrivateKeyPathIsRequired(): void
    {
        $this->expectException(InvalidConfigurationException::class);

        $this->process([
            'merchants' => [
                'default' => ['merchant_id' => 'M123456789'],
            ],
        ]);
    }

    public function testMerchantIdIsRequired(): void
    {
        $this->expectException(InvalidConfigurationException::class);

        $this->process([
            'merchants' => [
                'default' => ['private_key_path' => '/tmp/key.pem'],
            ],
        ]);
    }

    public function testMerchantPrivateKeyPassphraseDefaultsToEmptyString(): void
    {
        $config = $this->process([
            'merchants' => [
                'default' => [
                    'merchant_id' => 'M123456789',
                    'private_key_path' => '/tmp/key.pem',
                ],
            ],
        ]);

        $this->assertSame('', $config['merchants']['default']['private_key_passphrase']);
    }

    public function testCustomMipsPublicKeyPathIsPreserved(): void
    {
        $config = $this->process([
            'mips_public_key_path' => '/my/custom/key.pub',
            'merchants' => [
                'default' => [
                    'merchant_id' => 'M123456789',
                    'private_key_path' => '/tmp/key.pem',
                ],
            ],
        ]);

        $this->assertSame('/my/custom/key.pub', $config['mips_public_key_path']);
    }
}
