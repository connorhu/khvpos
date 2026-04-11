<?php declare(strict_types=1);

namespace KHTools\VPos\Payum\Action;

use KHTools\VPos\Payum\Constants;
use Payum\Core\Action\ActionInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Request\GetHumanStatus;

/**
 * Maps stored resultCode + paymentStatus model fields to Payum human status.
 * Does not make API calls — model must be updated first (by CaptureAction or SyncAction).
 */
class StatusAction implements ActionInterface
{
    public function execute($request): void
    {
        RequestNotSupportedException::assertSupports($this, $request);

        $model = ArrayObject::ensureArrayObject($request->getModel());

        if (!isset($model['payId'])) {
            $request->markNew();
            return;
        }

        $resultCode = (int) ($model['resultCode'] ?? -1);
        $paymentStatus = (int) ($model['paymentStatus'] ?? -1);

        if ($resultCode === Constants::RESULT_3DS_IN_PROGRESS) {
            $request->markPending();
            return;
        }

        if ($resultCode !== Constants::RESULT_OK) {
            $request->markFailed();
            return;
        }

        match ($paymentStatus) {
            Constants::STATUS_PENDING,
            Constants::STATUS_PROCESSING    => $request->markPending(),
            Constants::STATUS_CONFIRMED     => $request->markCaptured(),
            Constants::STATUS_AUTHORIZED    => $request->markAuthorized(),
            Constants::STATUS_CANCELLED,
            Constants::STATUS_REVERSED,
            Constants::STATUS_CLOSED        => $request->markCanceled(),
            default                         => $request->markUnknown(),
        };
    }

    public function supports($request): bool
    {
        return $request instanceof GetHumanStatus
            && $request->getModel() instanceof \ArrayAccess;
    }
}
