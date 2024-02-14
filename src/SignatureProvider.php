<?php declare(strict_types=1);

namespace KHTools\VPos;

use KHTools\VPos\Entities\Merchant;
use KHTools\VPos\Exceptions\SSLErrorException;
use KHTools\VPos\Keys\PrivateKey;
use KHTools\VPos\Keys\PublicKey;

class SignatureProvider implements SignatureProviderInterface
{
    private array $privateKeys;
    private PublicKey $mipsPublicKey;

    /**
     * @param array<string, PrivateKey> $privateKeys
     * @param string $mipsPublicKeyPath
     */
    public function __construct(
        array $privateKeys,
        private readonly string $mipsPublicKeyPath,
    )
    {
        foreach ($privateKeys as $merchantId => $privateKey) {
            $this->addPrivateKey($merchantId, $privateKey);
        }
        $this->mipsPublicKey = new PublicKey($this->mipsPublicKeyPath);
    }

    public function addPrivateKey(string $merchantId, PrivateKey|string $privateKey, ?string $privateKeyPassphrase = null): void
    {
        $this->privateKeys[$merchantId] = $privateKey instanceof PrivateKey ? $privateKey : new PrivateKey($privateKey, $privateKeyPassphrase);
    }

    private function getPrivateKeyWithMerchantId(string $merchantId): PrivateKey
    {
        $key = $this->privateKeys[$merchantId];

        if (!$key->isKeyLoaded()) {
            $key->load();
        }

        return $key;
    }

    private function buildStringContentToSign(array $contentToSign): string
    {
        $iterator = new \RecursiveIteratorIterator(new \RecursiveArrayIterator($contentToSign));

        $buffer = '';
        foreach ($iterator as $key => $item) {
            if ($key === 'signature') {
                continue;
            }

            if (is_bool($item)) {
                $item = $item === true ? 'true' : 'false';
            }
            $buffer .= $item .'|';
        }

        return rtrim($buffer, '|');
    }

    public function sign(Merchant $merchant, array $contentToSign): string
    {
        $signature = '';

        \openssl_sign($this->buildStringContentToSign($contentToSign), $signature, $this->getPrivateKeyWithMerchantId($merchant->merchantId)->getSSLKey(), OPENSSL_ALGO_SHA256);

        return \base64_encode($signature);
    }

    public function verify(array $signedContent, string $signature): bool
    {
        $data = $this->buildStringContentToSign($signedContent);
        $signature = base64_decode($signature);
        $result = \openssl_verify($data, $signature, $this->mipsPublicKey->getSSLKey(), OPENSSL_ALGO_SHA256);

        if ($result === 1) {
            return true;
        } elseif ($result === 0) {
            return false;
        }

        throw new SSLErrorException(sprintf('SSL error occurred: "%s"', openssl_error_string()));
    }
}