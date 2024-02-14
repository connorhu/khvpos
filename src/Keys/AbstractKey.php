<?php declare(strict_types=1);

namespace KHTools\VPos\Keys;

abstract class AbstractKey
{
    protected \OpenSSLAsymmetricKey $key;

    public function __construct(
        protected readonly string $keyPath,
        #[\SensitiveParameter]
        protected readonly ?string $passphrase = null)
    {
    }

    abstract protected function doLoadKey(): \OpenSSLAsymmetricKey;

    public function getSSLKey(): \OpenSSLAsymmetricKey
    {
        if (isset($this->key)) {
            return $this->key;
        }

        return $this->key = $this->doLoadKey();
    }

    public function load(): void
    {
        if ($this->isKeyLoaded()) {
            return;
        }

        $this->key = $this->doLoadKey();
    }

    public function isKeyLoaded(): bool
    {
        return isset($this->key);
    }
}