<?php declare(strict_types=1);

namespace KHTools\VPos\Exceptions;

class KeyNotReadableException extends \RuntimeException
{
    protected static function getLastOpenSSLErrorMessage(): string
    {
        $buffer = '';
        while ($message = \openssl_error_string()) {
            $buffer .= $message;
        }

        return $buffer;
    }

    public static function create(): self
    {
        return new self(sprintf('Failed to read private key. OpenSSL error: "%s".', self::getLastOpenSSLErrorMessage()));
    }
}