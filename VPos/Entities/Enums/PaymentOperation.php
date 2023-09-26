<?php declare(strict_types=1);

namespace KHTools\VPos\Entities\Enums;

enum PaymentOperation implements StringValueEnum
{
    case Payment;

    case OneClick;

    public function stringValue(): string
    {
        return match ($this) {
            PaymentOperation::Payment => 'payment',
            PaymentOperation::OneClick => 'oneclickPayment',
        };
    }
}