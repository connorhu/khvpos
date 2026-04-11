<?php declare(strict_types=1);

namespace KHTools\VPos\Payum\Action;

use KHTools\VPos\Models\Merchant;
use KHTools\VPos\Payum\Constants;
use KHTools\VPos\Requests\PaymentCloseRequest;
use KHTools\VPos\Requests\PaymentReverseRequest;
use KHTools\VPos\VPosClient;
use Payum\Core\Action\ActionInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Request\Cancel;

class CancelAction implements ActionInterface
{
    public function __construct(
        private readonly VPosClient $client,
        private readonly Merchant $merchant,
    ) {}

    public function execute($request): void
    {
        RequestNotSupportedException::assertSupports($this, $request);

        $model = ArrayObject::ensureArrayObject($request->getModel());
        $paymentStatus = (int) ($model['paymentStatus'] ?? 0);
        $payId = (string) $model['payId'];

        if ($payId === '') {
            throw new \LogicException('Cannot cancel payment: "payId" is not set in the model.');
        }

        if (in_array($paymentStatus, [Constants::STATUS_PENDING, Constants::STATUS_PROCESSING], true)) {
            $reverseRequest = new PaymentReverseRequest();
            $reverseRequest->setMerchant($this->merchant);
            $reverseRequest->setPaymentId($payId);
            $response = $this->client->send($reverseRequest);
        } elseif (in_array($paymentStatus, [Constants::STATUS_CANCELLED, Constants::STATUS_AUTHORIZED], true)) {
            $closeRequest = new PaymentCloseRequest();
            $closeRequest->setMerchant($this->merchant);
            $closeRequest->setPaymentId($payId);
            $response = $this->client->send($closeRequest);
        } elseif ($paymentStatus === Constants::STATUS_CONFIRMED) {
            throw new \LogicException(sprintf(
                'Cannot cancel payment "%s" with status %d (already captured). Use RefundAction instead.',
                $payId,
                $paymentStatus,
            ));
        } else {
            return; // already cancelled/reversed/closed — no-op
        }

        $model['resultCode'] = $response->getResultCode();
        $model['resultMessage'] = $response->getResultMessage();
    }

    public function supports($request): bool
    {
        return $request instanceof Cancel
            && $request->getModel() instanceof \ArrayAccess;
    }
}
