<?php declare(strict_types=1);

namespace KHTools\Tests\Requests;

use KHTools\VPos\Requests\ApplePayEchoRequest;
use KHTools\VPos\Requests\ApplePayInitRequest;
use KHTools\VPos\Requests\ApplePayProcessRequest;
use KHTools\VPos\Requests\EchoRequest;
use KHTools\VPos\Requests\GooglePayEchoRequest;
use KHTools\VPos\Requests\GooglePayInitRequest;
use KHTools\VPos\Requests\GooglePayProcessRequest;
use KHTools\VPos\Requests\OneClickEchoRequest;
use KHTools\VPos\Requests\OneClickInitRequest;
use KHTools\VPos\Requests\OneClickProcessRequest;
use KHTools\VPos\Requests\PaymentCloseRequest;
use KHTools\VPos\Requests\PaymentInitRequest;
use KHTools\VPos\Requests\PaymentProcessRequest;
use KHTools\VPos\Requests\PaymentRefundRequest;
use KHTools\VPos\Requests\PaymentReverseRequest;
use KHTools\VPos\Requests\PaymentStatusRequest;
use KHTools\VPos\Responses\ApplePayEchoResponse;
use KHTools\VPos\Responses\ApplePayInitResponse;
use KHTools\VPos\Responses\ApplePayProcessResponse;
use KHTools\VPos\Responses\EchoResponse;
use KHTools\VPos\Responses\GooglePayEchoResponse;
use KHTools\VPos\Responses\GooglePayInitResponse;
use KHTools\VPos\Responses\GooglePayProcessResponse;
use KHTools\VPos\Responses\OneClickEchoResponse;
use KHTools\VPos\Responses\OneClickInitResponse;
use KHTools\VPos\Responses\OneClickProcessResponse;
use KHTools\VPos\Responses\PaymentCloseResponse;
use KHTools\VPos\Responses\PaymentInitResponse;
use KHTools\VPos\Responses\PaymentRefundResponse;
use KHTools\VPos\Responses\PaymentReverseResponse;
use KHTools\VPos\Responses\PaymentStatusResponse;
use KHTools\VPos\Responses\ResponseInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class RequestMetadataTest extends TestCase
{
    /**
     * @return array<string, array{class-string, string, string, string}>
     */
    public static function requestMetadataProvider(): array
    {
        return [
            'EchoRequest'             => [EchoRequest::class,             'POST', '/echo',                                                          EchoResponse::class],
            'PaymentInitRequest'      => [PaymentInitRequest::class,      'POST', '/payment/init',                                                   PaymentInitResponse::class],
            'PaymentStatusRequest'    => [PaymentStatusRequest::class,    'GET',  '/payment/status/{merchantId}/{payId}/{dttm}/{signature}',          PaymentStatusResponse::class],
            'PaymentCloseRequest'     => [PaymentCloseRequest::class,     'PUT',  '/payment/close',                                                   PaymentCloseResponse::class],
            'PaymentReverseRequest'   => [PaymentReverseRequest::class,   'PUT',  '/payment/reverse',                                                 PaymentReverseResponse::class],
            'PaymentRefundRequest'    => [PaymentRefundRequest::class,    'PUT',  '/payment/refund',                                                  PaymentRefundResponse::class],
            'OneClickEchoRequest'     => [OneClickEchoRequest::class,     'POST', '/oneclick/echo',                                                   OneClickEchoResponse::class],
            'OneClickInitRequest'     => [OneClickInitRequest::class,     'POST', '/oneclick/init',                                                   OneClickInitResponse::class],
            'OneClickProcessRequest'  => [OneClickProcessRequest::class,  'POST', '/oneclick/process',                                                OneClickProcessResponse::class],
            'ApplePayEchoRequest'     => [ApplePayEchoRequest::class,     'POST', '/applepay/echo',                                                   ApplePayEchoResponse::class],
            'ApplePayInitRequest'     => [ApplePayInitRequest::class,     'POST', '/applepay/init',                                                   ApplePayInitResponse::class],
            'ApplePayProcessRequest'  => [ApplePayProcessRequest::class,  'POST', '/applepay/process',                                                ApplePayProcessResponse::class],
            'GooglePayEchoRequest'    => [GooglePayEchoRequest::class,    'POST', '/googlepay/echo',                                                  GooglePayEchoResponse::class],
            'GooglePayInitRequest'    => [GooglePayInitRequest::class,    'POST', '/googlepay/init',                                                  GooglePayInitResponse::class],
            'GooglePayProcessRequest' => [GooglePayProcessRequest::class, 'POST', '/googlepay/process',                                               GooglePayProcessResponse::class],
            'PaymentProcessRequest'   => [PaymentProcessRequest::class,   'GET',  '/payment/process/{merchantId}/{payId}/{dttm}/{signature}',          ''],
        ];
    }

    /**
     * @param class-string $requestClass
     */
    #[DataProvider('requestMetadataProvider')]
    public function testRequestMethod(string $requestClass, string $expectedMethod, string $expectedPath, string $expectedResponseClass): void
    {
        $request = new $requestClass();
        $this->assertSame($expectedMethod, $request->getRequestMethod());
    }

    /**
     * @param class-string $requestClass
     */
    #[DataProvider('requestMetadataProvider')]
    public function testEndpointPath(string $requestClass, string $expectedMethod, string $expectedPath, string $expectedResponseClass): void
    {
        $request = new $requestClass();
        $this->assertSame($expectedPath, $request->getEndpointPath());
    }

    /**
     * @param class-string $requestClass
     * @param class-string $expectedResponseClass
     */
    #[DataProvider('requestMetadataProvider')]
    public function testResponseClass(string $requestClass, string $expectedMethod, string $expectedPath, string $expectedResponseClass): void
    {
        if ($expectedResponseClass === '') {
            $this->markTestSkipped('PaymentProcessRequest::getResponseClass() intentionally throws — tested separately');
        }

        $request = new $requestClass();
        $actual = $request->getResponseClass();
        $this->assertSame($expectedResponseClass, $actual);
        $this->assertTrue(
            is_a($actual, ResponseInterface::class, true),
            sprintf('%s must implement ResponseInterface', $actual)
        );
    }

    public function testPaymentProcessRequestResponseClassThrows(): void
    {
        $this->expectException(\BadFunctionCallException::class);
        (new PaymentProcessRequest())->getResponseClass();
    }
}
