<?php

namespace KHTools\VPos\Normalizers;

use KHTools\VPos\Models\CartItem;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class CartItemNormalizer implements NormalizerInterface
{
    public function __construct(
        private readonly ObjectNormalizer $objectNormalizer,
    )
    {
    }

    public function normalize(mixed $object, string $format = null, array $context = []): array
    {
        $normalized = $this->objectNormalizer->normalize($object, $format, [
            AbstractObjectNormalizer::CALLBACKS => [
                'amount' => function (float $value, CartItem $cartItem) {
                    return $cartItem->getRawAmount();
                },
            ],
            AbstractObjectNormalizer::IGNORED_ATTRIBUTES => [
                'rawAmount',
            ],
            AbstractObjectNormalizer::SKIP_NULL_VALUES => true,
        ]);

        return NormalizerResultOrderingHelper::orderArray($normalized, [
            'name',
            'quantity',
            'amount',
            'description',
        ]);
    }

    public function supportsNormalization(mixed $data, string $format = null, array $context = []): bool
    {
        return $data instanceof CartItem;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            '*' => null,
            CartItem::class => true,
        ];
    }
}
