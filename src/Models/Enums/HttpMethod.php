<?php declare(strict_types=1);

namespace KHTools\VPos\Entities\Enums;

enum HttpMethod implements StringValueEnum
{
    case Get;

    case Post;

    public function stringValue(): string
    {
        return match ($this) {
            HttpMethod::Get => 'GET',
            HttpMethod::Post => 'POST',
        };
    }

    public static function initWithString(string $value): self
    {
        return match ($value) {
            'GET' => HttpMethod::Get,
            'POST' => HttpMethod::Post,
        };
    }
}