<?php

namespace KHTools\Tests\Keys;

use KHTools\VPos\Exceptions\KeyNotFoundException;
use KHTools\VPos\Exceptions\KeyNotReadableException;
use KHTools\VPos\Keys\PublicKey;
use PHPUnit\Framework\TestCase;

class PublicKeyTest extends TestCase
{
    public function testNotLoadedByDefault(): void
    {
        $key = new PublicKey('not-exitst');
        $this->assertFalse($key->isKeyLoaded());
    }

    public function testMissingKey(): void
    {
        $this->expectException(KeyNotFoundException::class);
        $this->expectExceptionMessage('Key not found at "not-exitst"');
        $key = new PublicKey('not-exitst');
        $key->load();
    }

    public function testLoadFailed(): void
    {
        $this->expectException(KeyNotReadableException::class);
        $this->expectExceptionMessageMatches('/Failed to read private key. OpenSSL error: ".+"\./');
        $key = new PublicKey(__DIR__.'/../Fixtures/empty_file.pem');
        $key->load();
    }

    public function testSuccessLoad(): void
    {
        $key = new PublicKey(__DIR__.'/../Fixtures/test1_public_key.pem');
        $key->load();
        $this->assertTrue($key->isKeyLoaded());
        $this->assertInstanceOf(\OpenSSLAsymmetricKey::class, $key->getSSLKey());
    }

    public function testDontLoadTwice(): void
    {
        $key = new PublicKey(__DIR__.'/../Fixtures/test1_public_key.pem');
        $key->load();
        $objectId = spl_object_id($key->getSSLKey());
        $key->load();
        $this->assertSame($objectId, spl_object_id($key->getSSLKey()));

        $key = new PublicKey(__DIR__.'/../Fixtures/test1_public_key.pem');
        $objectId = spl_object_id($key->getSSLKey());
        $this->assertSame($objectId, spl_object_id($key->getSSLKey()));
    }
}
