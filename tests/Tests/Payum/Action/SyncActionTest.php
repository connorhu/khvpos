<?php declare(strict_types=1);

namespace KHTools\Tests\Payum\Action;

use KHTools\VPos\Models\Merchant;
use KHTools\VPos\Payum\Action\SyncAction;
use KHTools\VPos\Payum\Constants;
use KHTools\VPos\Requests\PaymentStatusRequest;
use KHTools\VPos\Responses\PaymentStatusResponse;
use KHTools\VPos\VPosClient;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Request\Sync;
use PHPUnit\Framework\TestCase;

class SyncActionTest extends TestCase
{
    private function merchant(): Merchant
    {
        $m = new Merchant();
        $m->merchantId = 'M123';
        return $m;
    }

    public function testSupportsSyncWithArrayAccessModel(): void
    {
        $client = $this->createMock(VPosClient::class);
        $action = new SyncAction($client, $this->merchant());
        $this->assertTrue($action->supports(new Sync(new ArrayObject())));
    }

    public function testDoesNothingWhenNoPayIdInModel(): void
    {
        $client = $this->createMock(VPosClient::class);
        $client->expects($this->never())->method('send');

        $action = new SyncAction($client, $this->merchant());
        $model = new ArrayObject([]);
        $action->execute(new Sync($model));
    }

    public function testFetchesStatusAndUpdatesModel(): void
    {
        $statusResponse = new PaymentStatusResponse();
        $statusResponse->setPaymentId('pay-abc');
        $statusResponse->setResultCode(Constants::RESULT_OK);
        $statusResponse->setResultMessage('OK');
        $statusResponse->setPaymentStatus(Constants::STATUS_CONFIRMED);

        $client = $this->createMock(VPosClient::class);
        $client->expects($this->once())
            ->method('send')
            ->with($this->callback(fn ($r) => $r instanceof PaymentStatusRequest && $r->getPaymentId() === 'pay-abc'))
            ->willReturn($statusResponse);

        $action = new SyncAction($client, $this->merchant());
        $model = new ArrayObject(['payId' => 'pay-abc']);
        $action->execute(new Sync($model));

        $this->assertSame(Constants::RESULT_OK, $model['resultCode']);
        $this->assertSame(Constants::STATUS_CONFIRMED, $model['paymentStatus']);
        $this->assertSame('OK', $model['resultMessage']);
    }
}
