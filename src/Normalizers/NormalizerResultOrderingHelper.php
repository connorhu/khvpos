<?php

namespace KHTools\VPos\Normalizers;

use KHTools\VPos\Models\Address;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;


class NormalizerResultOrderingHelper
{
    public const ORDER = '__order';

    public static function orderArray(array $arrayToOrder, array $keyOrder): array
    {
        $buffer = [];
        foreach ($keyOrder as $key) {
            if (!isset($arrayToOrder[$key])) {
                continue;
            }

            $buffer[$key] = $arrayToOrder[$key];
        }

        return $buffer;
    }
}