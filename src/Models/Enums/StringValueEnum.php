<?php

namespace KHTools\VPos\Models\Enums;

interface StringValueEnum
{
    public function stringValue(): string;

    public static function initWithString(string $value): mixed;
}
