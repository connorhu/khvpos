<?php declare(strict_types=1);

namespace KHTools\VPos\Bundle\Providers;

use KHTools\VPos\Models\Merchant;

interface MerchantProviderInterface
{
    public function getMerchant(string $currency): Merchant;
}
