<?php declare(strict_types=1);

namespace KHTools\VPos\Payum\Action;

use KHTools\VPos\Models\Enums\Currency;
use KHTools\VPos\Models\Enums\HttpMethod;
use KHTools\VPos\Models\Merchant;
use KHTools\VPos\Requests\PaymentInitRequest;
use KHTools\VPos\Requests\PaymentProcessRequest;
use KHTools\VPos\VPosClient;
use Payum\Core\Action\ActionInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Request\Authorize;

class AuthorizeAction implements ActionInterface
{
    public function __construct(
        private readonly VPosClient $client,
        private readonly Merchant $merchant,
    ) {}

    public function execute($request): void
    {
        RequestNotSupportedException::assertSupports($this, $request);

        $model = ArrayObject::ensureArrayObject($request->getModel());

        if (isset($model['payId'])) {
            return; // already initialised
        }

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
        $model['authorization_url'] = $this->client->getPaymentUrlWithPaymentProcessRequest($processRequest);
    }

    public function supports($request): bool
    {
        return $request instanceof Authorize
            && $request->getModel() instanceof \ArrayAccess;
    }
}
