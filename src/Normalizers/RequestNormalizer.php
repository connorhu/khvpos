<?php

namespace KHTools\VPos\Normalizers;

use KHTools\VPos\Requests\RequestInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class RequestNormalizer implements NormalizerInterface
{
    public function __construct(
        private readonly ObjectNormalizer $objectNormalizer,
    ) {
    }

    /** @return array<string, mixed> */
    public function normalize(mixed $object, string $format = null, array $context = []): array
    {
        $context = $object->getNormalizationContext();
        $normalized = (array) $this->objectNormalizer->normalize($object, $format, $context);
        $normalized['dttm'] = date('YmdHis');

        if (isset($context[NormalizerResultOrderingHelper::ORDER])) {
            $normalized = NormalizerResultOrderingHelper::orderArray($normalized, $context[NormalizerResultOrderingHelper::ORDER]);
        }

        if (isset($normalized['order']['giftcards']) && count($normalized['order']['giftcards']) === 0) {
            unset($normalized['order']['giftcards']);
        }

        return $normalized;
    }

    public function supportsNormalization(mixed $data, string $format = null, array $context = []): bool
    {
        return $data instanceof RequestInterface;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            '*' => null,
            RequestInterface::class => true,
        ];
    }
}
