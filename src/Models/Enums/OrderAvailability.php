<?php declare(strict_types=1);

namespace KHTools\VPos\Models\Enums;

enum OrderAvailability implements StringValueEnum
{
    case Now;

    case PreOrder;

    public function stringValue(): string
    {
        return match ($this) {
            OrderAvailability::Now => 'now',
            OrderAvailability::PreOrder => 'preorder',
        };
    }

    public static function initWithString(string $value): self
    {
        return match ($value) {
            'now' => OrderAvailability::Now,
            'preorder' => OrderAvailability::PreOrder,
        };
    }
}
