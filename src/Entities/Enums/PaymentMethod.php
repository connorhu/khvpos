<?php declare(strict_types=1);

namespace KHTools\VPos\Entities\Enums;

enum PaymentMethod implements StringValueEnum
{
    case Card;

    case LowValuePayment;

    public function stringValue(): string
    {
        return match ($this) {
            PaymentMethod::Card => 'card',
            PaymentMethod::LowValuePayment => 'card#LVP',
        };
    }

    public static function initWithString(string $value): StringValueEnum
    {
        // TODO: Implement initWithString() method.
    }
}