<?php declare(strict_types=1);

namespace KHTools\Tests\Payum\Action;

use KHTools\VPos\Models\Merchant;
use KHTools\VPos\Payum\Action\CancelAction;
use KHTools\VPos\Payum\Constants;
use KHTools\VPos\Requests\PaymentCloseRequest;
use KHTools\VPos\Requests\PaymentReverseRequest;
use KHTools\VPos\Responses\PaymentCloseResponse;
use KHTools\VPos\Responses\PaymentReverseResponse;
use KHTools\VPos\VPosClient;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Request\Cancel;
use PHPUnit\Framework\TestCase;

class CancelActionTest extends TestCase
{
    private function merchant(): Merchant
    {
        $m = new Merchant();
        $m->merchantId = 'M123';
        return $m;
    }

    public function testSupportsCancelWithArrayAccessModel(): void
    {
        $client = $this->createMock(VPosClient::class);
        $action = new CancelAction($client, $this->merchant());
        $this->assertTrue($action->supports(new Cancel(new ArrayObject())));
    }

    public function testCallsReverseForStatusPending(): void
    {
        $reverseResponse = new PaymentReverseResponse();
        $reverseResponse->setResultCode(Constants::RESULT_OK);
        $reverseResponse->setResultMessage('OK');

        $client = $this->createMock(VPosClient::class);
        $client->expects($this->once())
            ->method('send')
            ->with($this->isInstanceOf(PaymentReverseRequest::class))
            ->willReturn($reverseResponse);

        $model = new ArrayObject(['payId' => 'pay-1', 'paymentStatus' => Constants::STATUS_PENDING]);
        (new CancelAction($client, $this->merchant()))->execute(new Cancel($model));
        $this->assertSame(Constants::RESULT_OK, $model['resultCode']);
    }

    public function testCallsReverseForStatusProcessing(): void
    {
        $reverseResponse = new PaymentReverseResponse();
        $reverseResponse->setResultCode(Constants::RESULT_OK);
        $reverseResponse->setResultMessage('OK');

        $client = $this->createMock(VPosClient::class);
        $client->expects($this->once())
            ->method('send')
            ->with($this->isInstanceOf(PaymentReverseRequest::class))
            ->willReturn($reverseResponse);

        $model = new ArrayObject(['payId' => 'pay-2', 'paymentStatus' => Constants::STATUS_PROCESSING]);
        (new CancelAction($client, $this->merchant()))->execute(new Cancel($model));
        $this->assertSame(Constants::RESULT_OK, $model['resultCode']);
    }

    public function testCallsCloseForStatusAuthorized(): void
    {
        $closeResponse = new PaymentCloseResponse();
        $closeResponse->setResultCode(Constants::RESULT_OK);
        $closeResponse->setResultMessage('OK');

        $client = $this->createMock(VPosClient::class);
        $client->expects($this->once())
            ->method('send')
            ->with($this->isInstanceOf(PaymentCloseRequest::class))
            ->willReturn($closeResponse);

        $model = new ArrayObject(['payId' => 'pay-3', 'paymentStatus' => Constants::STATUS_AUTHORIZED]);
        (new CancelAction($client, $this->merchant()))->execute(new Cancel($model));
        $this->assertSame(Constants::RESULT_OK, $model['resultCode']);
    }

    public function testCallsCloseForStatusCancelled(): void
    {
        $closeResponse = new PaymentCloseResponse();
        $closeResponse->setResultCode(Constants::RESULT_OK);
        $closeResponse->setResultMessage('OK');

        $client = $this->createMock(VPosClient::class);
        $client->expects($this->once())
            ->method('send')
            ->with($this->isInstanceOf(PaymentCloseRequest::class))
            ->willReturn($closeResponse);

        $model = new ArrayObject(['payId' => 'pay-5', 'paymentStatus' => Constants::STATUS_CANCELLED]);
        (new CancelAction($client, $this->merchant()))->execute(new Cancel($model));
        $this->assertSame(Constants::RESULT_OK, $model['resultCode']);
    }

    public function testThrowsLogicExceptionForAlreadyCapturedPayment(): void
    {
        $client = $this->createMock(VPosClient::class);
        $client->expects($this->never())->method('send');

        $model = new ArrayObject(['payId' => 'pay-4', 'paymentStatus' => Constants::STATUS_CONFIRMED]);
        $this->expectException(\LogicException::class);
        (new CancelAction($client, $this->merchant()))->execute(new Cancel($model));
    }
}
