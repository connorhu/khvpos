<?php declare(strict_types=1);

namespace KHTools\VPos\Payum\Action;

use Payum\Core\Action\ActionInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Model\PaymentInterface;
use Payum\Core\Request\Convert;

class ConvertPaymentAction implements ActionInterface
{
    public function execute($request): void
    {
        RequestNotSupportedException::assertSupports($this, $request);

        /** @var PaymentInterface $payment */
        $payment = $request->getSource();
        $details = ArrayObject::ensureArrayObject($payment->getDetails());

        $details['order_number'] = $payment->getNumber();
        $details['total_amount'] = $payment->getTotalAmount();
        $details['currency']     = $payment->getCurrencyCode();
        // return_url is required by PaymentInitRequest; an empty string will fail at the gateway layer
        $details['return_url']    = $details['return_url'] ?? '';
        $details['return_method'] = $details['return_method'] ?? 'POST';
        $details['merchant_id']   = $details['merchant_id'] ?? null;

        $request->setResult((array) $details);
    }

    public function supports($request): bool
    {
        return $request instanceof Convert
            && $request->getSource() instanceof PaymentInterface
            && $request->getTo() === 'array';
    }
}
