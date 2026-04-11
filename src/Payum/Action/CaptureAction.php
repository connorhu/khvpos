<?php declare(strict_types=1);

namespace KHTools\VPos\Payum\Action;

use KHTools\VPos\Models\Enums\Currency;
use KHTools\VPos\Models\Enums\HttpMethod;
use KHTools\VPos\Models\Merchant;
use KHTools\VPos\Requests\PaymentInitRequest;
use KHTools\VPos\Requests\PaymentProcessRequest;
use KHTools\VPos\Requests\PaymentStatusRequest;
use KHTools\VPos\VPosClient;
use Payum\Core\Action\ActionInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Reply\HttpRedirect;
use Payum\Core\Request\Capture;

class CaptureAction implements ActionInterface
{
    public function __construct(
        private readonly VPosClient $client,
        private readonly Merchant $merchant,
    ) {}

    public function execute($request): void
    {
        RequestNotSupportedException::assertSupports($this, $request);

        $model = ArrayObject::ensureArrayObject($request->getModel());

        if (!isset($model['payId'])) {
            $this->initPayment($model);
        } else {
            $this->refreshStatus($model);
        }
    }

    private function initPayment(ArrayObject $model): never
    {
        $initRequest = new PaymentInitRequest();
        $initRequest->setMerchant($this->merchant);
        $initRequest->setOrderNumber((string) $model['order_number']);
        $initRequest->setTotalAmount((float) ($model['total_amount'] / 100));
        $initRequest->setCurrency(Currency::initWithString((string) $model['currency']));
        $initRequest->setReturnUrl((string) $model['return_url']);
        $initRequest->setReturnMethod(HttpMethod::initWithString((string) ($model['return_method'] ?? 'POST')));

        $initResponse = $this->client->send($initRequest);

        $model['payId'] = $initResponse->getPaymentId();
        $model['paymentStatus'] = $initResponse->getPaymentStatus();
        $model['resultCode'] = $initResponse->getResultCode();
        $model['resultMessage'] = $initResponse->getResultMessage();

        $processRequest = PaymentProcessRequest::initWith($initResponse, $this->merchant);
        $url = $this->client->getPaymentUrlWithPaymentProcessRequest($processRequest);

        throw new HttpRedirect($url);
    }

    private function refreshStatus(ArrayObject $model): void
    {
        $statusRequest = new PaymentStatusRequest();
        $statusRequest->setMerchant($this->merchant);
        $statusRequest->setPaymentId((string) $model['payId']);

        $statusResponse = $this->client->send($statusRequest);

        $model['resultCode']    = $statusResponse->getResultCode();
        $model['paymentStatus'] = $statusResponse->getPaymentStatus() ?? $model['paymentStatus'];
        $model['resultMessage'] = $statusResponse->getResultMessage();
    }

    public function supports($request): bool
    {
        return $request instanceof Capture
            && $request->getModel() instanceof \ArrayAccess;
    }
}
