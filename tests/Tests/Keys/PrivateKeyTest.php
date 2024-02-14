<?php

namespace KHTools\Tests\Keys;

use KHTools\VPos\Exceptions\KeyNotFoundException;
use KHTools\VPos\Exceptions\KeyNotReadableException;
use KHTools\VPos\Keys\PrivateKey;
use PHPUnit\Framework\TestCase;

class PrivateKeyTest extends TestCase
{
    public function testNotLoadedByDefault(): void
    {
        $key = new PrivateKey('not-exitst');
        $this->assertFalse($key->isKeyLoaded());
    }

    public function testMissingKey(): void
    {
        $this->expectException(KeyNotFoundException::class);
        $this->expectExceptionMessage('Key not found at "not-exitst"');
        $key = new PrivateKey('not-exitst');
        $key->load();
    }

    public function testLoadFailed(): void
    {
        $this->expectException(KeyNotReadableException::class);
        $this->expectExceptionMessageMatches('/Failed to read private key. OpenSSL error: ".+"\./');
        $key = new PrivateKey(__DIR__.'/../Fixtures/empty_file.pem');
        $key->load();
    }

    public function testSuccessLoad(): void
    {
        $key = new PrivateKey(__DIR__.'/../Fixtures/test1_private_key.pem');
        $key->load();
        $this->assertTrue($key->isKeyLoaded());
        $this->assertInstanceOf(\OpenSSLAsymmetricKey::class, $key->getSSLKey());
    }

    public function testDontLoadTwice(): void
    {
        $key = new PrivateKey(__DIR__.'/../Fixtures/test1_private_key.pem');
        $key->load();
        $objectId = spl_object_id($key->getSSLKey());
        $key->load();
        $this->assertSame($objectId, spl_object_id($key->getSSLKey()));

        $key = new PrivateKey(__DIR__.'/../Fixtures/test1_private_key.pem');
        $objectId = spl_object_id($key->getSSLKey());
        $this->assertSame($objectId, spl_object_id($key->getSSLKey()));
    }
}
