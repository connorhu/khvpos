<?php declare(strict_types=1);

namespace KHTools\VPos\Exceptions;

class KeyNotFoundException extends \RuntimeException
{
    public static function notFoundAt(string $path): self
    {
        return new self(sprintf('Key not found at "%s"', $path));
    }
}