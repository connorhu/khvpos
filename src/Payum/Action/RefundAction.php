<?php declare(strict_types=1);

namespace KHTools\VPos\Payum\Action;

use KHTools\VPos\Models\Merchant;
use KHTools\VPos\Requests\PaymentRefundRequest;
use KHTools\VPos\VPosClient;
use Payum\Core\Action\ActionInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Request\Refund;

class RefundAction implements ActionInterface
{
    public function __construct(
        private readonly VPosClient $client,
        private readonly Merchant $merchant,
    ) {}

    public function execute($request): void
    {
        RequestNotSupportedException::assertSupports($this, $request);

        $model = ArrayObject::ensureArrayObject($request->getModel());

        $refundRequest = new PaymentRefundRequest();
        $refundRequest->setMerchant($this->merchant);
        $refundRequest->setPaymentId((string) $model['payId']);

        if (isset($model['refund_amount'])) {
            // refund_amount is stored in minor units (fillér); setAmount expects float HUF
            $refundRequest->setAmount((float) bcdiv((string) ((int) $model['refund_amount']), '100', 2));
        }

        $response = $this->client->send($refundRequest);

        $model['resultCode'] = $response->getResultCode();
        $model['resultMessage'] = $response->getResultMessage();
    }

    public function supports($request): bool
    {
        return $request instanceof Refund
            && $request->getModel() instanceof \ArrayAccess;
    }
}
