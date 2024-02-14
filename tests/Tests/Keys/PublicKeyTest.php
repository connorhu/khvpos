<?php

namespace KHTools\Tests\Keys;

use KHTools\VPos\Exceptions\KeyNotFoundException;
use KHTools\VPos\Exceptions\KeyNotReadableException;
use KHTools\VPos\Keys\PublicKey;
use PHPUnit\Framework\TestCase;

class PublicKeyTest extends TestCase
{
    public function testNotLoadedByDefault()
    {
        $key = new PublicKey('not-exitst');
        $this->assertFalse($key->isKeyLoaded());
    }

    public function testMissingKey()
    {
        $this->expectException(KeyNotFoundException::class);
        $this->expectExceptionMessage('Key not found at "not-exitst"');
        $key = new PublicKey('not-exitst');
        $key->load();
    }

    public function testLoadFailed()
    {
        $this->expectException(KeyNotReadableException::class);
        $this->expectExceptionMessageMatches('/Failed to read private key. OpenSSL error: ".+"\./');
        $key = new PublicKey(__DIR__.'/../Fixtures/empty_file.pem');
        $key->load();
    }

    public function testSuccessLoad()
    {
        $key = new PublicKey(__DIR__.'/../Fixtures/test1_public_key.pem');
        $key->load();
        $this->assertTrue($key->isKeyLoaded());
        $this->assertInstanceOf(\OpenSSLAsymmetricKey::class, $key->getSSLKey());
    }
}
