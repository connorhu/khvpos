<?php declare(strict_types=1);

namespace KHTools\Tests\Bundle;

use KHTools\VPos\Bundle\Providers\MerchantProvider;
use KHTools\VPos\Exceptions\InvalidArgumentException;
use KHTools\VPos\Models\Merchant;
use PHPUnit\Framework\TestCase;

class MerchantProviderTest extends TestCase
{
    private MerchantProvider $provider;

    protected function setUp(): void
    {
        $this->provider = new MerchantProvider([
            'HUF' => 'M123456789',
            'EUR' => 'M987654321',
        ]);
    }

    public function testGetMerchantReturnsCorrectMerchantForKnownCurrency(): void
    {
        $merchant = $this->provider->getMerchant('HUF');

        $this->assertInstanceOf(Merchant::class, $merchant);
        $this->assertSame('M123456789', $merchant->merchantId);
    }

    public function testGetMerchantReturnsCorrectMerchantForSecondCurrency(): void
    {
        $merchant = $this->provider->getMerchant('EUR');

        $this->assertSame('M987654321', $merchant->merchantId);
    }

    public function testGetMerchantThrowsForUnknownCurrency(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unsupported currency: "USD"');

        $this->provider->getMerchant('USD');
    }
}
