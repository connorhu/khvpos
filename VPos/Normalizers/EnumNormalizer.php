<?php

namespace KHTools\VPos\Normalizers;

use KHTools\VPos\Entities\Enums\StringValueEnum;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class EnumNormalizer implements NormalizerInterface
{
    /**
     * @param StringValueEnum $object
     * @param string|null $format
     * @param array $context
     * @return string
     */
    public function normalize(mixed $object, string $format = null, array $context = [])
    {
        return $object->stringValue();
    }

    public function supportsNormalization(mixed $data, string $format = null): bool
    {
        return $data instanceof StringValueEnum;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            '*' => null,
            StringValueEnum::class => true,
        ];
    }
}