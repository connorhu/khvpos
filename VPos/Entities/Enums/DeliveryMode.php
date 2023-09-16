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
}