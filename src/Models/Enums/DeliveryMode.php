<?php declare(strict_types=1);

namespace KHTools\VPos\Entities\Enums;

enum DeliveryMode implements StringValueEnum
{
    case Electronic;

    case SameDay;

    case NextDay;

    case TwoDaysLater;

    public function stringValue(): string
    {
        return match ($this) {
            DeliveryMode::Electronic => '0',
            DeliveryMode::SameDay => '1',
            DeliveryMode::NextDay => '2',
            DeliveryMode::TwoDaysLater => '3',
        };
    }

    public static function initWithString(string $value): self
    {
        return match ($value) {
            '0' => DeliveryMode::Electronic,
            '1' => DeliveryMode::SameDay,
            '2' => DeliveryMode::NextDay,
            '3' => DeliveryMode::TwoDaysLater,
        };
    }
}
