<?php declare(strict_types=1);

namespace KHTools\VPos\Models;

use KHTools\VPos\Models\Enums\DeliveryMode;
use KHTools\VPos\Models\Enums\OrderAvailability;
use KHTools\VPos\Models\Enums\OrderDelivery;
use KHTools\VPos\Models\Enums\OrderType;

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
