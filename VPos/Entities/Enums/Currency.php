<?php declare(strict_types=1);

namespace KHTools\VPos\Entities\Enums;

use KHTools\VPos\TransactionInterface;

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

    public static function enumWithString(string $currency): Currency
    {
        return match ($currency) {
            'EUR' => Currency::EUR,
            'HUF' => Currency::HUF,
            'USD' => Currency::USD,
        };
    }

    public static function enumWithTransactionCurrency(int $currency): Currency
    {
        return match ($currency) {
            TransactionInterface::TRANSACTION_EUR_CURRENCY => Currency::EUR,
            TransactionInterface::TRANSACTION_HUF_CURRENCY => Currency::HUF,
            TransactionInterface::TRANSACTION_USD_CURRENCY => Currency::USD,
        };
    }
}