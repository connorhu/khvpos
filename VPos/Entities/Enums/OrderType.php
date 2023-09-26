<?php declare(strict_types=1);

namespace KHTools\VPos\Entities\Enums;

enum OrderType implements StringValueEnum
{
    case Type;
    case Purchase;
    case Balance;
    case Prepaid;
    case Cash;
    case Check;

    public function stringValue(): string
    {
        return match ($this) {
            OrderType::Type => 'type',
            OrderType::Purchase => 'purchase',
            OrderType::Balance => 'balance',
            OrderType::Prepaid => 'prepaid',
            OrderType::Cash => 'cash',
            OrderType::Check => 'check',
        };
    }
}