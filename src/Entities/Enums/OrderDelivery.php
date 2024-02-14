<?php declare(strict_types=1);

namespace KHTools\VPos\Entities\Enums;

enum OrderDelivery implements StringValueEnum
{
    case Shipping;
    case ShippingVerified;
    case InStore;
    case Digital;
    case Ticket;
    case Other;

    public function stringValue(): string
    {
        return match ($this) {
            OrderDelivery::Shipping => 'shipping',
            OrderDelivery::ShippingVerified => 'shipping_verified',
            OrderDelivery::InStore => 'instore',
            OrderDelivery::Digital => 'digital',
            OrderDelivery::Ticket => 'ticket',
            OrderDelivery::Other => 'other',
        };
    }

    public static function initWithString(string $value): StringValueEnum
    {
        // TODO: Implement initWithString() method.
    }
}