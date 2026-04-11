<?php declare(strict_types=1);

namespace KHTools\VPos\Models;

use KHTools\VPos\Models\Enums\Currency;

class GiftCard
{
    private ?int $totalAmount = null;

    private ?Currency $currency = null;

    private ?int $quantity = null;

    public function getTotalAmount(): ?int
    {
        return $this->totalAmount;
    }

    public function setTotalAmount(?int $totalAmount): void
    {
        $this->totalAmount = $totalAmount;
    }

    public function getCurrency(): ?Currency
    {
        return $this->currency;
    }

    public function setCurrency(?Currency $currency): void
    {
        $this->currency = $currency;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(?int $quantity): void
    {
        $this->quantity = $quantity;
    }
}
