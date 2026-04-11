<?php declare(strict_types=1);

namespace KHTools\Tests\Payum\Action;

use KHTools\VPos\Models\Merchant;
use KHTools\VPos\Payum\Action\RefundAction;
use KHTools\VPos\Payum\Constants;
use KHTools\VPos\Requests\PaymentRefundRequest;
use KHTools\VPos\Responses\PaymentRefundResponse;
use KHTools\VPos\VPosClient;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Request\Refund;
use PHPUnit\Framework\TestCase;

class RefundActionTest extends TestCase
{
    private function merchant(): Merchant
    {
        $m = new Merchant();
        $m->merchantId = 'M123';
        return $m;
    }

    public function testSupportsRefundWithArrayAccessModel(): void
    {
        $client = $this->createMock(VPosClient::class);
        $action = new RefundAction($client, $this->merchant());
        $this->assertTrue($action->supports(new Refund(new ArrayObject())));
    }

    public function testCallsPaymentRefundRequestAndUpdatesModel(): void
    {
        $refundResponse = new PaymentRefundResponse();
        $refundResponse->setResultCode(Constants::RESULT_OK);
        $refundResponse->setResultMessage('OK');

        $client = $this->createMock(VPosClient::class);
        $client->expects($this->once())
            ->method('send')
            ->with($this->callback(fn ($r) =>
                $r instanceof PaymentRefundRequest
                && $r->getPaymentId() === 'pay-refund'
            ))
            ->willReturn($refundResponse);

        $model = new ArrayObject(['payId' => 'pay-refund']);
        $action = new RefundAction($client, $this->merchant());
        $action->execute(new Refund($model));

        $this->assertSame(Constants::RESULT_OK, $model['resultCode']);
        $this->assertSame('OK', $model['resultMessage']);
    }

    public function testPassesRefundAmountWhenPresent(): void
    {
        $refundResponse = new PaymentRefundResponse();
        $refundResponse->setResultCode(Constants::RESULT_OK);
        $refundResponse->setResultMessage('OK');

        $capturedRequest = null;
        $client = $this->createMock(VPosClient::class);
        $client->method('send')
            ->willReturnCallback(function ($request) use (&$capturedRequest, $refundResponse) {
                $capturedRequest = $request;
                return $refundResponse;
            });

        $model = new ArrayObject(['payId' => 'pay-refund', 'refund_amount' => 5000]);
        $action = new RefundAction($client, $this->merchant());
        $action->execute(new Refund($model));

        $this->assertInstanceOf(PaymentRefundRequest::class, $capturedRequest);
        // 5000 fillér = 50.00 HUF; getRawAmount() should be 5000
        $this->assertSame(5000, $capturedRequest->getRawAmount());
    }
}
