<?php declare(strict_types=1);

namespace KHTools\VPos\Models;

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
    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
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
    public function setQuantity(?int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
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

    /**
     * @return int|null
     */
    public function getRawAmount(): ?int
    {
        return $this->amount;
    }

    /**
     * The total price for the stated number of items in hundredths of a currency; the currency will be automatically taken from the currency item of the entire request.
     *
     * @param float|null $amount
     */
    public function setAmount(?float $amount): self
    {
        $this->amount = (int) round($amount * 100);

        return $this;
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
    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }
}
