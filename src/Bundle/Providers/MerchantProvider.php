<?php declare(strict_types=1);

namespace KHTools\VPos\Bundle\Providers;

use KHTools\VPos\Exceptions\InvalidArgumentException;
use KHTools\VPos\Models\Merchant;

class MerchantProvider implements MerchantProviderInterface
{
    /**
     * @param array<string, string> $merchantIds currency ISO code => merchant ID
     */
    public function __construct(private readonly array $merchantIds)
    {
    }

    public function getMerchant(string $currency): Merchant
    {
        $merchant = new Merchant();
        $merchant->merchantId = $this->merchantIds[$currency]
            ?? throw new InvalidArgumentException(sprintf('Unsupported currency: "%s"', $currency));

        return $merchant;
    }
}
