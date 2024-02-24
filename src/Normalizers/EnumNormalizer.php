<?php

namespace KHTools\VPos\Normalizers;

use KHTools\VPos\Models\Enums\StringValueEnum;
use KHTools\VPos\Responses\ResponseInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class EnumNormalizer implements NormalizerInterface, DenormalizerInterface
{
    /**
     * @param StringValueEnum $object
     * @param string|null $format
     * @param array $context
     * @return string
     */
    public function normalize(mixed $object, string $format = null, array $context = []): string
    {
        return $object->stringValue();
    }

    public function supportsNormalization(mixed $data, string $format = null, array $context = []): bool
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

    public function denormalize(mixed $data, string $type, string $format = null, array $context = []): object
    {
        return $type::initWithString($data);
    }

    public function supportsDenormalization(mixed $data, string $type, string $format = null, array $context = []): bool
    {
        return (class_implements($type)[StringValueEnum::class] ?? null) === StringValueEnum::class;
    }
}
