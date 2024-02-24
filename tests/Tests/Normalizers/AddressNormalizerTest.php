<?php

namespace KHTools\Tests\Normalizers;

use KHTools\VPos\Entities\Address;
use KHTools\VPos\Normalizers\AddressNormalizer;
use KHTools\VPos\Normalizers\CartItemNormalizer;
use KHTools\VPos\Normalizers\EnumNormalizer;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Mapping\Loader\AttributeLoader;
use Symfony\Component\Serializer\NameConverter\MetadataAwareNameConverter;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class AddressNormalizerTest extends TestCase
{
    private NormalizerInterface $normalizer;

    protected function setUp(): void
    {
        $loader = class_exists(AttributeLoader::class) ? new AttributeLoader() : new AnnotationLoader();
        $classMetadataFactory = new ClassMetadataFactory($loader);
        $metadataAwareNameConverter = new MetadataAwareNameConverter($classMetadataFactory);

        $dateTimeNormalizer = new DateTimeNormalizer();
        $objectNormalizer = new ObjectNormalizer($classMetadataFactory, $metadataAwareNameConverter);

        $this->normalizer = new Serializer([
            new AddressNormalizer($objectNormalizer),
            new CartItemNormalizer($objectNormalizer),
            new EnumNormalizer(),
            $dateTimeNormalizer,
            $objectNormalizer,
        ]);
    }

    #[DataProvider(methodName: 'paymentInitDataProvider')]
    public function testPaymentInit(Address $address, array $expected)
    {
        $result = $this->normalizer->normalize($address);

        $this->assertSame($expected, $result);
    }

    public static function paymentInitDataProvider(): \Generator
    {
        $address = new Address();
        yield [$address, []];

        $address = new Address();
        $address->city = 'Budapest';
        yield [$address, [
            'city' => 'Budapest',
        ]];

        $address = new Address();
        $address->zip = '1111';
        yield [$address, [
            'zip' => '1111',
        ]];

        $address = new Address();
        $address->zip = '1111';
        yield [$address, [
            'zip' => '1111',
        ]];

        $address = new Address();
        $address->state = 'HU-PE';
        yield [$address, [
            'state' => 'HU-PE',
        ]];

        $address = new Address();
        $address->country = 'HUN';
        yield [$address, [
            'country' => 'HUN',
        ]];

        $address = new Address();
        $address->address = 'Sajt utca 1.';
        yield [$address, [
            'address1' => 'Sajt utca 1.',
        ]];

        // we should separate the address parts like the nav onlineszamla api detail address type
        $address = new Address();
        $address->address = 'Csontváry Kosztka Tivadar utca 14., V. épület 1. emelet 4.';
        yield [$address, [
            'address1' => 'Csontváry Kosztka Tivadar utca 14., V. épület 1.',
            'address2' => 'emelet 4.',
        ]];
    }
}
