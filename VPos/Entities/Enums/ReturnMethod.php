<?php declare(strict_types=1);

namespace KHTools\VPos\Entities\Enums;

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
}