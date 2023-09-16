<?php declare(strict_types=1);

namespace KHTools\VPos\Entities;

class CartItem
{
    private ?string $name = null;

    private ?int $quantity = null;

    private ?int $amount = null;

    private ?string $description = null;

    /**
     * Product name
     *
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Product name, maximum length 20 characters.
     *
     * @param string|null $name
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * Amount
     *
     * @return int|null
     */
    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    /**
     * Amount, must be >=1, integer.
     *
     * @param int|null $quantity
     */
    public function setQuantity(?int $quantity): void
    {
        $this->quantity = $quantity;
    }

    /**
     * The total price for the stated number of items in hundredths of a currency; the currency will be automatically taken from the currency item of the entire request.
     *
     * @return float|null
     */
    public function getAmount(): ?float
    {
        return $this->amount / 100;
    }

    public function getRawAmount(): int
    {
        return $this->amount;
    }

    /**
     * The total price for the stated number of items in hundredths of a currency; the currency will be automatically taken from the currency item of the entire request.
     *
     * @param float|null $amount
     */
    public function setAmount(?float $amount): void
    {
        $this->amount = (int) round($amount * 100);
    }

    /**
     * Cart item description
     *
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Cart item description, maximum length 40 characters.
     *
     * @param string|null $description
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }
}