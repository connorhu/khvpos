<?php

namespace KHTools\Tests\Normalizers;

use KHTools\VPos\Models\Enums\Currency;
use KHTools\VPos\Models\Enums\CustomerLoginAuth;
use KHTools\VPos\Models\Enums\DeliveryMode;
use KHTools\VPos\Models\Enums\HttpMethod;
use KHTools\VPos\Models\Enums\Language;
use KHTools\VPos\Models\Enums\OrderAvailability;
use KHTools\VPos\Models\Enums\OrderDelivery;
use KHTools\VPos\Models\Enums\OrderType;
use KHTools\VPos\Models\Enums\PaymentMethod;
use KHTools\VPos\Models\Enums\PaymentOperation;
use KHTools\VPos\Normalizers\EnumNormalizer;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class EnumNormalizerTest extends TestCase
{
    private EnumNormalizer $normalizer;

    protected function setUp(): void
    {
        $this->normalizer = new EnumNormalizer();
    }

    #[DataProvider(methodName: 'languageDenormalizeDataProvider')]
    public function testLanguageDenormalize(string $type, string $language, string $expectedStringValue): void
    {
        $enum = $this->normalizer->denormalize($language, $type);
        $this->assertSame($expectedStringValue, $enum->stringValue());
    }

    public static function languageDenormalizeDataProvider(): \Generator
    {
        yield [Language::class, 'hu', 'hu'];
        yield [Language::class, 'en', 'en'];
        yield [Language::class, 'de', 'de'];
        yield [Language::class, 'fr', 'fr'];
        yield [Language::class, 'cs', 'cs'];
        yield [Language::class, 'it', 'it'];
        yield [Language::class, 'ja', 'ja'];
        yield [Language::class, 'pl', 'pl'];
        yield [Language::class, 'pt', 'pt'];
        yield [Language::class, 'ro', 'ro'];
        yield [Language::class, 'ru', 'ru'];
        yield [Language::class, 'sk', 'sk'];
        yield [Language::class, 'es', 'es'];
        yield [Language::class, 'tr', 'tr'];
        yield [Language::class, 'vi', 'vi'];
        yield [Language::class, 'hr', 'hr'];
        yield [Language::class, 'sl', 'sl'];
        yield [Language::class, 'sv', 'sv'];

        yield [HttpMethod::class, 'POST', 'POST'];
        yield [HttpMethod::class, 'GET', 'GET'];

        yield [HttpMethod::class, 'POST', 'POST'];
        yield [HttpMethod::class, 'GET', 'GET'];

        yield [CustomerLoginAuth::class, 'guest', 'guest'];
        yield [CustomerLoginAuth::class, 'account', 'account'];
        yield [CustomerLoginAuth::class, 'federated', 'federated'];
        yield [CustomerLoginAuth::class, 'issuer', 'issuer'];
        yield [CustomerLoginAuth::class, 'thirdparty', 'thirdparty'];
        yield [CustomerLoginAuth::class, 'fido', 'fido'];
        yield [CustomerLoginAuth::class, 'fido_signed', 'fido_signed'];
        yield [CustomerLoginAuth::class, 'api', 'api'];

        yield [DeliveryMode::class, '0', '0'];
        yield [DeliveryMode::class, '1', '1'];
        yield [DeliveryMode::class, '2', '2'];
        yield [DeliveryMode::class, '3', '3'];

        yield [Currency::class, 'EUR', 'EUR'];
        yield [Currency::class, 'HUF', 'HUF'];
        yield [Currency::class, 'USD', 'USD'];

        yield [PaymentOperation::class, 'payment', 'payment'];
        yield [PaymentOperation::class, 'oneclickPayment', 'oneclickPayment'];

        yield [PaymentMethod::class, 'card', 'card'];
        yield [PaymentMethod::class, 'card#LVP', 'card#LVP'];

        yield [PaymentMethod::class, 'card#LVP', 'card#LVP'];

        yield [OrderType::class, 'type', 'type'];
        yield [OrderType::class, 'purchase', 'purchase'];
        yield [OrderType::class, 'balance', 'balance'];
        yield [OrderType::class, 'prepaid', 'prepaid'];
        yield [OrderType::class, 'cash', 'cash'];
        yield [OrderType::class, 'check', 'check'];

        yield [OrderDelivery::class, 'shipping', 'shipping'];
        yield [OrderDelivery::class, 'shipping_verified', 'shipping_verified'];
        yield [OrderDelivery::class, 'instore', 'instore'];
        yield [OrderDelivery::class, 'digital', 'digital'];
        yield [OrderDelivery::class, 'ticket', 'ticket'];
        yield [OrderDelivery::class, 'other', 'other'];

        yield [OrderAvailability::class, 'now', 'now'];
        yield [OrderAvailability::class, 'preorder', 'preorder'];

    }
}
