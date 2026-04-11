<?php declare(strict_types=1);

namespace KHTools\VPos\Payum\Action;

use KHTools\VPos\Models\Merchant;
use KHTools\VPos\Requests\PaymentStatusRequest;
use KHTools\VPos\VPosClient;
use Payum\Core\Action\ActionInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Request\Sync;

class SyncAction implements ActionInterface
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
            return;
        }

        $statusRequest = new PaymentStatusRequest();
        $statusRequest->setMerchant($this->merchant);
        $statusRequest->setPaymentId($model['payId']);

        $response = $this->client->send($statusRequest);

        $model['resultCode'] = $response->getResultCode();
        $model['paymentStatus'] = $response->getPaymentStatus();
        $model['resultMessage'] = $response->getResultMessage();
    }

    public function supports($request): bool
    {
        return $request instanceof Sync
            && $request->getModel() instanceof \ArrayAccess;
    }
}
