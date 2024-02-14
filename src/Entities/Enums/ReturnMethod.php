<?php

namespace KHTools\VPos\Entities\Enums;

/**
 * @deprecated
 * @see HttpMethod
 */
enum ReturnMethod implements StringValueEnum
{
    case Get;

    case Post;

    public function stringValue(): string
    {
        return match ($this) {
            ReturnMethod::Get => 'GET',
            ReturnMethod::Post => 'POST',
        };
    }

    public static function initWithString(string $value): StringValueEnum
    {
        // TODO: Implement initWithString() method.
    }
}