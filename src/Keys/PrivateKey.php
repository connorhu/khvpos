<?php declare(strict_types=1);

namespace KHTools\VPos\Keys;

use KHTools\VPos\Exceptions\KeyNotFoundException;
use KHTools\VPos\Exceptions\KeyNotReadableException;

final class PrivateKey extends AbstractKey
{
    protected function doLoadKey(): \OpenSSLAsymmetricKey
    {
        if (!is_file($this->keyPath)) {
            throw KeyNotFoundException::notFoundAt($this->keyPath);
        }

        $path = $this->keyPath;

        if (!str_starts_with($path, 'file://')) {
            $path = 'file://'.$path;
        }

        $publicKey = openssl_get_privatekey($path, $this->passphrase);

        if ($publicKey === false) {
            throw KeyNotReadableException::create();
        }

        return $publicKey;
    }
}