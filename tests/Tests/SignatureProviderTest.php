<?php declare(strict_types=1);

namespace KHTools\Tests;

use KHTools\VPos\Exceptions\SSLErrorException;
use KHTools\VPos\Keys\PrivateKey;
use KHTools\VPos\Models\Merchant;
use KHTools\VPos\SignatureProvider;
use PHPUnit\Framework\TestCase;

class SignatureProviderTest extends TestCase
{
    private const FIXTURES = __DIR__ . '/Fixtures';

    private function merchant(string $id = 'M123'): Merchant
    {
        $m = new Merchant();
        $m->merchantId = $id;
        return $m;
    }

    private function provider(): SignatureProvider
    {
        return new SignatureProvider(
            ['M123' => new PrivateKey(self::FIXTURES . '/test1_private_key.pem')],
            self::FIXTURES . '/test1_public_key.pem',
        );
    }

    public function testSignProducesValidRsaSha256Signature(): void
    {
        $provider = $this->provider();
        $payload = ['merchantId' => 'M123', 'dttm' => '20240101120000', 'orderNo' => '1001'];

        $signature = $provider->sign($this->merchant(), $payload);

        $decoded = base64_decode($signature, true);
        $this->assertNotFalse($decoded, 'Signature must be valid base64');

        $pubKey = openssl_pkey_get_public(file_get_contents(self::FIXTURES . '/test1_public_key.pem'));
        $result = openssl_verify('M123|20240101120000|1001', $decoded, $pubKey, OPENSSL_ALGO_SHA256);
        $this->assertSame(1, $result, 'openssl_verify must confirm the signature');
    }

    public function testSignAcceptsPrivateKeyPathString(): void
    {
        $provider = new SignatureProvider([], self::FIXTURES . '/test1_public_key.pem');
        $provider->addPrivateKey('M123', self::FIXTURES . '/test1_private_key.pem');

        // If lazy loading works, sign() must not throw
        $signature = $provider->sign($this->merchant(), ['merchantId' => 'M123']);
        $this->assertNotEmpty($signature);
        $this->assertNotFalse(base64_decode($signature, true), 'Signature must be valid base64');
    }

    public function testVerifyReturnsTrueForCorrectSignature(): void
    {
        $provider = $this->provider();
        $payload = ['merchantId' => 'M123', 'dttm' => '20240101120000'];

        $signature = $provider->sign($this->merchant(), $payload);

        // New provider with test1_public_key as MIPS key — same key used for signing
        $verifier = new SignatureProvider([], self::FIXTURES . '/test1_public_key.pem');
        $this->assertTrue($verifier->verify($payload, $signature));
    }

    public function testVerifyReturnsFalseForTamperedContent(): void
    {
        $provider = $this->provider();
        $payload = ['merchantId' => 'M123', 'dttm' => '20240101120000'];
        $signature = $provider->sign($this->merchant(), $payload);

        $tampered = $payload;
        $tampered['merchantId'] = 'EVIL';

        $verifier = new SignatureProvider([], self::FIXTURES . '/test1_public_key.pem');
        $this->assertFalse($verifier->verify($tampered, $signature));
    }

    public function testVerifyThrowsForInvalidBase64Signature(): void
    {
        $provider = new SignatureProvider([], self::FIXTURES . '/test1_public_key.pem');
        $this->expectException(SSLErrorException::class);
        $provider->verify(['merchantId' => 'M123'], '!!!!');
    }

    public function testSignBooleanTrueSerializesAsStringTrue(): void
    {
        $provider = $this->provider();
        $payload = ['merchantId' => 'M123', 'flag' => true];

        $signature = $provider->sign($this->merchant(), $payload);
        $decoded = base64_decode($signature, true);
        $pubKey = openssl_pkey_get_public(file_get_contents(self::FIXTURES . '/test1_public_key.pem'));

        // 'true' string literal expected (not '1')
        $result = openssl_verify('M123|true', $decoded, $pubKey, OPENSSL_ALGO_SHA256);
        $this->assertSame(1, $result);
    }

    public function testSignBooleanFalseSerializesAsStringFalse(): void
    {
        $provider = $this->provider();
        $payload = ['merchantId' => 'M123', 'flag' => false];

        $signature = $provider->sign($this->merchant(), $payload);
        $decoded = base64_decode($signature, true);
        $pubKey = openssl_pkey_get_public(file_get_contents(self::FIXTURES . '/test1_public_key.pem'));

        $result = openssl_verify('M123|false', $decoded, $pubKey, OPENSSL_ALGO_SHA256);
        $this->assertSame(1, $result);
    }

    public function testSignSkipsSignatureKey(): void
    {
        $provider = $this->provider();
        // 'signature' key must be excluded from the signed string
        $payload = ['merchantId' => 'M123', 'signature' => 'should-be-skipped'];

        $signature = $provider->sign($this->merchant(), $payload);
        $decoded = base64_decode($signature, true);
        $pubKey = openssl_pkey_get_public(file_get_contents(self::FIXTURES . '/test1_public_key.pem'));

        // Only 'M123' — the signature value is NOT in the signed string
        $result = openssl_verify('M123', $decoded, $pubKey, OPENSSL_ALGO_SHA256);
        $this->assertSame(1, $result);
    }

    public function testSignFlattensNestedArraysRecursively(): void
    {
        $provider = $this->provider();
        $payload = ['merchantId' => 'M123', 'nested' => ['a' => 'x', 'b' => 'y']];

        $signature = $provider->sign($this->merchant(), $payload);
        $decoded = base64_decode($signature, true);
        $pubKey = openssl_pkey_get_public(file_get_contents(self::FIXTURES . '/test1_public_key.pem'));

        // PHP preserves array insertion order; 'a' is visited before 'b' deterministically
        $result = openssl_verify('M123|x|y', $decoded, $pubKey, OPENSSL_ALGO_SHA256);
        $this->assertSame(1, $result);
    }
}
