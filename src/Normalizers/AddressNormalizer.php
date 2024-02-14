<?php

namespace KHTools\VPos\Normalizers;

use KHTools\VPos\Entities\Address;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;


class AddressNormalizer implements NormalizerInterface
{
    public function __construct(private readonly ObjectNormalizer $objectNormalizer)
    {
    }

    /**
     * @param Address $object
     * @param string|null $format
     * @param array $context
     * @return array
     */
    public function normalize(mixed $object, string $format = null, array $context = []): array
    {
        $normalized = $this->objectNormalizer->normalize($object, $format, [
            AbstractObjectNormalizer::IGNORED_ATTRIBUTES => [
                'address',
            ],
            AbstractObjectNormalizer::SKIP_NULL_VALUES => true,
        ]);

        if ($object->address !== null) {
            $addressBuffer = [1 => null, 2 => null, 3 => null];
            $buffer = '';
            $pointer = 1;

            foreach (explode(' ', $object->address) as $word) {
                if (mb_strlen($buffer.' '.$word) > 50 && $pointer < 3) {
                    $addressBuffer[$pointer] = ltrim($buffer);
                    $buffer = $word;
                    $pointer++;
                    continue;
                }

                $buffer .= ' '.$word;
            }

            $addressBuffer[$pointer] = ltrim($buffer);

            foreach ($addressBuffer as $addressNumber => $addressValue) {
                if ($addressValue === null) {
                    continue;
                }

                $normalized['address'.$addressNumber] = $addressValue;
            }
        }

        return NormalizerResultOrderingHelper::orderArray($normalized, [
            'address1',
            'address2',
            'address3',
            'city',
            'zip',
            'state',
            'country',
        ]);
    }

    public function supportsNormalization(mixed $data, string $format = null, array $context = []): bool
    {
        return $data instanceof Address;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            '*' => null,
            Address::class => true,
        ];
    }
}
