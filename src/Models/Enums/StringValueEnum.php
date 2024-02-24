<?php

namespace KHTools\VPos\Entities\Enums;

interface StringValueEnum
{
    public function stringValue(): string;

    public static function initWithString(string $value): mixed;
}
