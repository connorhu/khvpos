<?php declare(strict_types=1);

namespace KHTools\VPos\Entities;

use KHTools\VPos\Entities\Enums\DeliveryMode;
use KHTools\VPos\Entities\Enums\OrderAvailability;
use KHTools\VPos\Entities\Enums\OrderDelivery;
use KHTools\VPos\Entities\Enums\OrderType;

class Order
{
    public ?OrderType $type = null;

    public ?OrderAvailability $availability = null;

    public ?OrderDelivery $delivery = null;

    public ?DeliveryMode $deliveryMode = null;

    /**
     * e-mail address to which the merchant delivers electronic goods (gift card codes, etc.), max. length 100 characters.
     *
     * @var string|null
     */
    public ?string $deliveryEmail = null;

    public ?bool $nameMatch = null;

    public ?bool $addressMatch = null;

    public ?Address $billing = null;

    public ?Address $shipping = null;

    public ?\DateTime $shippingAddedAt = null;

    public ?bool $reorder = null;

    public ?array $giftcards = [];
}
