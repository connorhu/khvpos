<?php declare(strict_types=1);

namespace KHTools\VPos\Entities\Enums;

enum Currency implements StringValueEnum
{
    case EUR;

    case HUF;

    case USD;

    public function stringValue(): string
    {
        return match ($this) {
            Currency::EUR => 'EUR',
            Currency::HUF => 'HUF',
            Currency::USD => 'USD',
        };
    }

    public static function stringValues(): array
    {
        return [
            'EUR',
            'HUF',
            'USD',
        ];
    }

    /**
     * @deprecated
     * @see self::initWithString()
     */
    public static function enumWithString(string $currency): Currency
    {
        return self::initWithString($currency);
    }

    public static function initWithString(string $value): self
    {
        return match ($value) {
            'EUR' => Currency::EUR,
            'HUF' => Currency::HUF,
            'USD' => Currency::USD,
        };
    }
}
