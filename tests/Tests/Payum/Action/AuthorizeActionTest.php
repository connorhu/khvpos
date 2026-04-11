<?php declare(strict_types=1);

namespace KHTools\Tests\Payum\Action;

use KHTools\VPos\Models\Merchant;
use KHTools\VPos\Payum\Action\AuthorizeAction;
use KHTools\VPos\Payum\Constants;
use KHTools\VPos\Requests\PaymentInitRequest;
use KHTools\VPos\Requests\PaymentProcessRequest;
use KHTools\VPos\Responses\PaymentInitResponse;
use KHTools\VPos\VPosClient;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Request\Authorize;
use PHPUnit\Framework\TestCase;

class AuthorizeActionTest extends TestCase
{
    private function merchant(): Merchant
    {
        $m = new Merchant();
        $m->merchantId = 'M123';
        return $m;
    }

    public function testSupportsAuthorizeWithArrayAccessModel(): void
    {
        $client = $this->createMock(VPosClient::class);
        $action = new AuthorizeAction($client, $this->merchant());
        $this->assertTrue($action->supports(new Authorize(new ArrayObject())));
    }

    public function testCallsPaymentInitAndStoresUrlWithoutRedirecting(): void
    {
        $initResponse = new PaymentInitResponse();
        $initResponse->setPaymentId('pay-auth');
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
            ->willReturn('https://api.khpos.hu/pay-auth-url');

        $model = new ArrayObject([
            'order_number'  => 'ORDER-3',
            'total_amount'  => 2000,
            'currency'      => 'HUF',
            'return_url'    => 'https://example.com/return',
            'return_method' => 'POST',
        ]);
        $action = new AuthorizeAction($client, $this->merchant());
        $action->execute(new Authorize($model)); // must NOT throw HttpRedirect

        $this->assertSame('pay-auth', $model['payId']);
        $this->assertSame('https://api.khpos.hu/pay-auth-url', $model['authorization_url']);
        $this->assertSame(Constants::STATUS_PENDING, $model['paymentStatus']);
        $this->assertSame(Constants::RESULT_OK,      $model['resultCode']);
        $this->assertSame('OK',                      $model['resultMessage']);
    }
}
