<?php declare(strict_types=1);

namespace KHTools\Tests\Payum\Action;

use KHTools\VPos\Models\Merchant;
use KHTools\VPos\Payum\Action\CaptureAction;
use KHTools\VPos\Payum\Constants;
use KHTools\VPos\Requests\PaymentInitRequest;
use KHTools\VPos\Requests\PaymentProcessRequest;
use KHTools\VPos\Requests\PaymentStatusRequest;
use KHTools\VPos\Responses\PaymentInitResponse;
use KHTools\VPos\Responses\PaymentStatusResponse;
use KHTools\VPos\VPosClient;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Reply\HttpRedirect;
use Payum\Core\Request\Capture;
use PHPUnit\Framework\TestCase;

class CaptureActionTest extends TestCase
{
    private function merchant(): Merchant
    {
        $m = new Merchant();
        $m->merchantId = 'M123';
        return $m;
    }

    public function testSupportsCaptureWithArrayAccessModel(): void
    {
        $client = $this->createMock(VPosClient::class);
        $action = new CaptureAction($client, $this->merchant());
        $this->assertTrue($action->supports(new Capture(new ArrayObject())));
    }

    public function testFirstPassCallsPaymentInitAndThrowsHttpRedirect(): void
    {
        $initResponse = new PaymentInitResponse();
        $initResponse->setPaymentId('pay-new');
        $initResponse->setPaymentStatus(Constants::STATUS_PENDING);
        $initResponse->setResultCode(Constants::RESULT_OK);
        $initResponse->setResultMessage('OK');

        $client = $this->createMock(VPosClient::class);
        $client->expects($this->once())
            ->method('send')
            ->with($this->isInstanceOf(PaymentInitRequest::class))
            ->willReturn($initResponse);
        $client->expects($this->once())
            ->method('getPaymentUrlWithPaymentProcessRequest')
            ->with($this->isInstanceOf(PaymentProcessRequest::class))
            ->willReturn('https://api.khpos.hu/api/v1.0/payment/process/M123/pay-new/...');

        $model = new ArrayObject([
            'order_number' => 'ORDER-1',
            'total_amount' => 10000,
            'currency'     => 'HUF',
            'return_url'   => 'https://example.com/return',
            'return_method' => 'POST',
        ]);
        $capture = new Capture($model);
        $action = new CaptureAction($client, $this->merchant());

        $this->expectException(HttpRedirect::class);
        $action->execute($capture);
    }

    public function testFirstPassStoresPayIdBeforeRedirect(): void
    {
        $initResponse = new PaymentInitResponse();
        $initResponse->setPaymentId('pay-stored');
        $initResponse->setPaymentStatus(Constants::STATUS_PENDING);
        $initResponse->setResultCode(Constants::RESULT_OK);
        $initResponse->setResultMessage('OK');

        $client = $this->createMock(VPosClient::class);
        $client->method('send')->willReturn($initResponse);
        $client->method('getPaymentUrlWithPaymentProcessRequest')->willReturn('https://example.com/pay');

        $model = new ArrayObject([
            'order_number' => 'ORDER-2',
            'total_amount' => 5000,
            'currency'     => 'HUF',
            'return_url'   => 'https://example.com/return',
            'return_method' => 'POST',
        ]);
        $action = new CaptureAction($client, $this->merchant());

        try {
            $action->execute(new Capture($model));
        } catch (HttpRedirect) {
            // Expected
        }

        $this->assertSame('pay-stored', $model['payId']);
        $this->assertSame(Constants::STATUS_PENDING, $model['paymentStatus']);
        $this->assertSame(Constants::RESULT_OK, $model['resultCode']);
        $this->assertSame('OK', $model['resultMessage']);
    }

    public function testSecondPassCallsPaymentStatusAndUpdatesModel(): void
    {
        $statusResponse = new PaymentStatusResponse();
        $statusResponse->setPaymentId('pay-exist');
        $statusResponse->setResultCode(Constants::RESULT_OK);
        $statusResponse->setResultMessage('OK');
        $statusResponse->setPaymentStatus(Constants::STATUS_CONFIRMED);

        $client = $this->createMock(VPosClient::class);
        $client->expects($this->once())
            ->method('send')
            ->with($this->callback(fn ($r) => $r instanceof PaymentStatusRequest && $r->getPaymentId() === 'pay-exist'))
            ->willReturn($statusResponse);
        $client->expects($this->never())->method('getPaymentUrlWithPaymentProcessRequest');

        $model = new ArrayObject(['payId' => 'pay-exist']);
        $action = new CaptureAction($client, $this->merchant());
        $action->execute(new Capture($model));

        $this->assertSame(Constants::RESULT_OK, $model['resultCode']);
        $this->assertSame(Constants::STATUS_CONFIRMED, $model['paymentStatus']);
        $this->assertSame('OK', $model['resultMessage']);
    }
}
