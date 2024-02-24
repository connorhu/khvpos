<?php declare(strict_types=1);

namespace KHTools\VPos\Models\Enums;

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

    public static function initWithString(string $value): self
    {
        return match ($value) {
            'type' => OrderType::Type,
            'purchase' => OrderType::Purchase,
            'balance' => OrderType::Balance,
            'prepaid' => OrderType::Prepaid,
            'cash' => OrderType::Cash,
            'check' => OrderType::Check,
        };
    }
}
