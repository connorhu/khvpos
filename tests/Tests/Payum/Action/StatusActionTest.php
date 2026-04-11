<?php declare(strict_types=1);

namespace KHTools\Tests\Payum\Action;

use KHTools\VPos\Payum\Action\StatusAction;
use KHTools\VPos\Payum\Constants;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Request\GetHumanStatus;
use PHPUnit\Framework\TestCase;

class StatusActionTest extends TestCase
{
    private StatusAction $action;

    protected function setUp(): void
    {
        $this->action = new StatusAction();
    }

    public function testSupportsGetHumanStatusWithArrayAccessModel(): void
    {
        $status = new GetHumanStatus(new ArrayObject());
        $this->assertTrue($this->action->supports($status));
    }

    public function testDoesNotSupportOtherRequests(): void
    {
        $this->assertFalse($this->action->supports(new \stdClass()));
    }

    public function testMarksNewWhenNoPayIdInModel(): void
    {
        $model = new ArrayObject([]);
        $status = new GetHumanStatus($model);
        $this->action->execute($status);
        $this->assertTrue($status->isNew());
    }

    public function testMarksCapturedForResultCode0AndPaymentStatus4(): void
    {
        $model = new ArrayObject([
            'payId'         => 'pay-123',
            'resultCode'    => Constants::RESULT_OK,
            'paymentStatus' => Constants::STATUS_CONFIRMED,
        ]);
        $status = new GetHumanStatus($model);
        $this->action->execute($status);
        $this->assertTrue($status->isCaptured());
    }

    public function testMarksPendingForPaymentStatus1(): void
    {
        $model = new ArrayObject([
            'payId'         => 'pay-123',
            'resultCode'    => Constants::RESULT_OK,
            'paymentStatus' => Constants::STATUS_PENDING,
        ]);
        $status = new GetHumanStatus($model);
        $this->action->execute($status);
        $this->assertTrue($status->isPending());
    }

    public function testMarksPendingForPaymentStatus2(): void
    {
        $model = new ArrayObject([
            'payId'         => 'pay-123',
            'resultCode'    => Constants::RESULT_OK,
            'paymentStatus' => Constants::STATUS_PROCESSING,
        ]);
        $status = new GetHumanStatus($model);
        $this->action->execute($status);
        $this->assertTrue($status->isPending());
    }

    public function testMarksPendingFor3DsInProgress(): void
    {
        $model = new ArrayObject([
            'payId'         => 'pay-123',
            'resultCode'    => Constants::RESULT_3DS_IN_PROGRESS,
            'paymentStatus' => Constants::STATUS_PENDING,
        ]);
        $status = new GetHumanStatus($model);
        $this->action->execute($status);
        $this->assertTrue($status->isPending());
    }

    public function testMarksAuthorizedForPaymentStatus5(): void
    {
        $model = new ArrayObject([
            'payId'         => 'pay-123',
            'resultCode'    => Constants::RESULT_OK,
            'paymentStatus' => Constants::STATUS_AUTHORIZED,
        ]);
        $status = new GetHumanStatus($model);
        $this->action->execute($status);
        $this->assertTrue($status->isAuthorized());
    }

    public function testMarksCanceledForPaymentStatus3(): void
    {
        $model = new ArrayObject([
            'payId'         => 'pay-123',
            'resultCode'    => Constants::RESULT_OK,
            'paymentStatus' => Constants::STATUS_CANCELLED,
        ]);
        $status = new GetHumanStatus($model);
        $this->action->execute($status);
        $this->assertTrue($status->isCanceled());
    }

    public function testMarksCanceledForPaymentStatus7(): void
    {
        $model = new ArrayObject([
            'payId'         => 'pay-123',
            'resultCode'    => Constants::RESULT_OK,
            'paymentStatus' => Constants::STATUS_REVERSED,
        ]);
        $status = new GetHumanStatus($model);
        $this->action->execute($status);
        $this->assertTrue($status->isCanceled());
    }

    public function testMarksCanceledForPaymentStatus8(): void
    {
        $model = new ArrayObject([
            'payId'         => 'pay-123',
            'resultCode'    => Constants::RESULT_OK,
            'paymentStatus' => Constants::STATUS_CLOSED,
        ]);
        $status = new GetHumanStatus($model);
        $this->action->execute($status);
        $this->assertTrue($status->isCanceled());
    }

    public function testMarksFailedForNonZeroResultCode(): void
    {
        $model = new ArrayObject([
            'payId'         => 'pay-123',
            'resultCode'    => 400,
            'paymentStatus' => Constants::STATUS_REJECTED,
        ]);
        $status = new GetHumanStatus($model);
        $this->action->execute($status);
        $this->assertTrue($status->isFailed());
    }

    public function testMarksUnknownForUnrecognizedPaymentStatus(): void
    {
        $model = new ArrayObject([
            'payId'         => 'pay-123',
            'resultCode'    => Constants::RESULT_OK,
            'paymentStatus' => Constants::STATUS_REJECTED, // 6 — no mapping in StatusAction
        ]);
        $status = new GetHumanStatus($model);
        $this->action->execute($status);
        $this->assertTrue($status->isUnknown());
    }
}
