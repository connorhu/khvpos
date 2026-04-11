<?php declare(strict_types=1);

namespace KHTools\VPos\Models;

use KHTools\VPos\Models\Enums\DeliveryMode;
use KHTools\VPos\Models\Enums\OrderAvailability;
use KHTools\VPos\Models\Enums\OrderDelivery;
use KHTools\VPos\Models\Enums\OrderType;
use Symfony\Component\Serializer\Annotation\SerializedName;

class Order
{
    private ?OrderType $type = null;

    private ?OrderAvailability $availability = null;

    private ?OrderDelivery $delivery = null;

    private ?DeliveryMode $deliveryMode = null;

    /**
     * e-mail address to which the merchant delivers electronic goods (gift card codes, etc.), max. length 100 characters.
     *
     * @var string|null
     */
    private ?string $deliveryEmail = null;

    private ?bool $nameMatch = null;

    private ?bool $addressMatch = null;

    private ?Address $billing = null;

    private ?Address $shipping = null;

    private ?\DateTime $shippingAddedAt = null;

    private ?bool $reorder = null;

    /** @var array<int, GiftCard> */
    #[SerializedName(serializedName: 'giftcards')]
    private array $giftCards = [];

    /**
     * @return OrderType|null
     */
    public function getType(): ?OrderType
    {
        return $this->type;
    }

    /**
     * @param OrderType|null $type
     */
    public function setType(?OrderType $type): void
    {
        $this->type = $type;
    }

    /**
     * @return OrderAvailability|null
     */
    public function getAvailability(): ?OrderAvailability
    {
        return $this->availability;
    }

    /**
     * @param OrderAvailability|null $availability
     */
    public function setAvailability(?OrderAvailability $availability): void
    {
        $this->availability = $availability;
    }

    /**
     * @return OrderDelivery|null
     */
    public function getDelivery(): ?OrderDelivery
    {
        return $this->delivery;
    }

    /**
     * @param OrderDelivery|null $delivery
     */
    public function setDelivery(?OrderDelivery $delivery): void
    {
        $this->delivery = $delivery;
    }

    /**
     * @return DeliveryMode|null
     */
    public function getDeliveryMode(): ?DeliveryMode
    {
        return $this->deliveryMode;
    }

    /**
     * @param DeliveryMode|null $deliveryMode
     */
    public function setDeliveryMode(?DeliveryMode $deliveryMode): void
    {
        $this->deliveryMode = $deliveryMode;
    }

    /**
     * @return string|null
     */
    public function getDeliveryEmail(): ?string
    {
        return $this->deliveryEmail;
    }

    /**
     * @param string|null $deliveryEmail
     */
    public function setDeliveryEmail(?string $deliveryEmail): void
    {
        $this->deliveryEmail = $deliveryEmail;
    }

    /**
     * @return bool|null
     */
    public function getNameMatch(): ?bool
    {
        return $this->nameMatch;
    }

    /**
     * @param bool|null $nameMatch
     */
    public function setNameMatch(?bool $nameMatch): void
    {
        $this->nameMatch = $nameMatch;
    }

    /**
     * @return bool|null
     */
    public function getAddressMatch(): ?bool
    {
        return $this->addressMatch;
    }

    /**
     * @param bool|null $addressMatch
     */
    public function setAddressMatch(?bool $addressMatch): void
    {
        $this->addressMatch = $addressMatch;
    }

    /**
     * @return Address|null
     */
    public function getBilling(): ?Address
    {
        return $this->billing;
    }

    /**
     * @param Address|null $billing
     */
    public function setBilling(?Address $billing): void
    {
        $this->billing = $billing;
    }

    /**
     * @return Address|null
     */
    public function getShipping(): ?Address
    {
        return $this->shipping;
    }

    /**
     * @param Address|null $shipping
     */
    public function setShipping(?Address $shipping): void
    {
        $this->shipping = $shipping;
    }

    /**
     * @return \DateTime|null
     */
    public function getShippingAddedAt(): ?\DateTime
    {
        return $this->shippingAddedAt;
    }

    /**
     * @param \DateTime|null $shippingAddedAt
     */
    public function setShippingAddedAt(?\DateTime $shippingAddedAt): void
    {
        $this->shippingAddedAt = $shippingAddedAt;
    }

    /**
     * @return bool|null
     */
    public function getReorder(): ?bool
    {
        return $this->reorder;
    }

    /**
     * @param bool|null $reorder
     */
    public function setReorder(?bool $reorder): void
    {
        $this->reorder = $reorder;
    }

    /**
     * @return array<int, GiftCard>
     */
    public function getGiftCards(): array
    {
        return $this->giftCards;
    }

    /**
     * @param array<int, GiftCard> $giftCards
     */
    public function setGiftCards(array $giftCards): void
    {
        $this->giftCards = $giftCards;
    }
}
