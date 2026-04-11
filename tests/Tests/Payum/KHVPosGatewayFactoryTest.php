<?php declare(strict_types=1);

namespace KHTools\Tests\Payum;

use KHTools\VPos\Payum\KHVPosGatewayFactory;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\GatewayInterface;
use Payum\Core\Request\GetHumanStatus;
use PHPUnit\Framework\TestCase;

class KHVPosGatewayFactoryTest extends TestCase
{
    private const FIXTURES = __DIR__ . '/../Fixtures';

    public function testCreateReturnsGatewayInterface(): void
    {
        $factory = new KHVPosGatewayFactory();
        $gateway = $factory->create([
            'merchant_id'          => 'M123',
            'private_key_path'     => self::FIXTURES . '/test1_private_key.pem',
            'mips_public_key_path' => self::FIXTURES . '/test1_public_key.pem',
        ]);
        $this->assertInstanceOf(GatewayInterface::class, $gateway);

        // Verify StatusAction is wired (no client dependency — safe to invoke)
        $model = new ArrayObject([]);
        $status = new GetHumanStatus($model);
        $gateway->execute($status);
        $this->assertTrue($status->isNew());
    }

    public function testCreateThrowsWhenMerchantIdMissing(): void
    {
        $factory = new KHVPosGatewayFactory();
        $this->expectException(\InvalidArgumentException::class);
        $factory->create(['private_key_path' => '/tmp/key.pem']);
    }

    public function testCreateThrowsWhenPrivateKeyPathMissing(): void
    {
        $factory = new KHVPosGatewayFactory();
        $this->expectException(\InvalidArgumentException::class);
        $factory->create(['merchant_id' => 'M123']);
    }
}
