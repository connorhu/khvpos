<?php declare(strict_types=1);

namespace KHTools\Tests\Responses;

use KHTools\VPos\Responses\EchoResponse;
use PHPUnit\Framework\TestCase;

class CommonResponseTraitTest extends TestCase
{
    public function testGetResultCodeReturnsNullBeforeDeserialization(): void
    {
        $response = new EchoResponse();
        $this->assertNull($response->getResultCode());
    }

    public function testGetResultMessageReturnsNullBeforeDeserialization(): void
    {
        $response = new EchoResponse();
        $this->assertNull($response->getResultMessage());
    }

    public function testGetResultCodeReturnsIntAfterSet(): void
    {
        $response = new EchoResponse();
        $response->setResultCode(0);
        $this->assertSame(0, $response->getResultCode());
    }

    public function testGetResultMessageReturnsStringAfterSet(): void
    {
        $response = new EchoResponse();
        $response->setResultMessage('OK');
        $this->assertSame('OK', $response->getResultMessage());
    }
}
