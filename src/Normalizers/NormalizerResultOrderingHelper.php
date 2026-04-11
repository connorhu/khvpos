<?php

namespace KHTools\VPos\Normalizers;

class NormalizerResultOrderingHelper
{
    public const ORDER = '__order';

    /**
     * @param array<string, mixed> $arrayToOrder
     * @param list<string> $keyOrder
     * @return array<string, mixed>
     */
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
