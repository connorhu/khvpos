<?php

namespace KHTools\VPos\Exceptions;

class HttpErrorException extends \RuntimeException
{
    public static function getErrorClassWithResponseCode(int $statusCode): string
    {
        return match ($statusCode) {
            400, 401, 403, 404, 405, 429 => ClientErrorException::class,
            500, 503 => ServerErrorException::class,
            default => throw new UnhandledErrorException(sprintf('Unknown http status code: %d', $statusCode)),
        };
    }
}