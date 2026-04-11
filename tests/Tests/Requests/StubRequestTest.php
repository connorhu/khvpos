<?php declare(strict_types=1);

namespace KHTools\Tests\Requests;

use KHTools\VPos\Requests\GooglePayEchoRequest;
use KHTools\VPos\Requests\GooglePayInitRequest;
use KHTools\VPos\Requests\GooglePayProcessRequest;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class StubRequestTest extends TestCase
{
    /** @return array<string, array{class-string}> */
    public static function stubClassProvider(): array
    {
        return [
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
