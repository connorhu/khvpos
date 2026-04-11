<?php declare(strict_types=1);

namespace KHTools\Tests\Requests;

use KHTools\VPos\Requests\ApplePayEchoRequest;
use KHTools\VPos\Requests\ApplePayInitRequest;
use KHTools\VPos\Requests\ApplePayProcessRequest;
use KHTools\VPos\Requests\GooglePayEchoRequest;
use KHTools\VPos\Requests\GooglePayInitRequest;
use KHTools\VPos\Requests\GooglePayProcessRequest;
use KHTools\VPos\Requests\OneClickEchoRequest;
use KHTools\VPos\Requests\OneClickInitRequest;
use KHTools\VPos\Requests\OneClickProcessRequest;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class StubRequestTest extends TestCase
{
    /** @return array<string, array{class-string}> */
    public static function stubClassProvider(): array
    {
        return [
            'OneClickInitRequest' => [OneClickInitRequest::class],
            'OneClickProcessRequest' => [OneClickProcessRequest::class],
            'ApplePayInitRequest' => [ApplePayInitRequest::class],
            'ApplePayProcessRequest' => [ApplePayProcessRequest::class],
            'GooglePayInitRequest' => [GooglePayInitRequest::class],
            'GooglePayProcessRequest' => [GooglePayProcessRequest::class],
        ];
    }

    #[DataProvider(methodName: 'stubClassProvider')]
    public function testGetEndpointPathThrowsLogicException(string $class): void
    {
        $request = new $class();
        $this->expectException(\LogicException::class);
        $request->getEndpointPath();
    }

    /** @return array<string, array{class-string}> */
    public static function allStubClassProvider(): array
    {
        return [
            'OneClickInitRequest' => [OneClickInitRequest::class],
            'OneClickEchoRequest' => [OneClickEchoRequest::class],
            'OneClickProcessRequest' => [OneClickProcessRequest::class],
            'ApplePayInitRequest' => [ApplePayInitRequest::class],
            'ApplePayEchoRequest' => [ApplePayEchoRequest::class],
            'ApplePayProcessRequest' => [ApplePayProcessRequest::class],
            'GooglePayInitRequest' => [GooglePayInitRequest::class],
            'GooglePayEchoRequest' => [GooglePayEchoRequest::class],
            'GooglePayProcessRequest' => [GooglePayProcessRequest::class],
        ];
    }

    #[DataProvider(methodName: 'allStubClassProvider')]
    public function testGetResponseClassThrowsLogicException(string $class): void
    {
        $request = new $class();
        $this->expectException(\LogicException::class);
        $request->getResponseClass();
    }
}
