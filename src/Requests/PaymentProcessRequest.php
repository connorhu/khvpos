<?php declare(strict_types=1);

namespace KHTools\VPos\Requests;

use KHTools\VPos\Models\Merchant;
use KHTools\VPos\Requests\Traits\MerchantTrait;
use KHTools\VPos\Requests\Traits\PaymentIdTrait;
use KHTools\VPos\Responses\PaymentInitResponse;
use Symfony\Component\Serializer\Annotation\Ignore;

class PaymentProcessRequest implements RequestInterface
{
    use MerchantTrait;
    use PaymentIdTrait;

    public static function initWith(PaymentInitResponse $paymentInitResponse, Merchant $merchant): self
    {
        $instance = new self();
        $instance->merchant = $merchant;
        $instance->paymentId = $paymentInitResponse->getPaymentId();

        return $instance;
    }

    #[Ignore]
    public function getRequestMethod(): string
    {
        return 'GET';
    }

    #[Ignore]
    public function getEndpointPath(): string
    {
        return '/payment/process/{merchantId}/{payId}/{dttm}/{signature}';
    }

    #[Ignore]
    public function getResponseClass(): string
    {
        throw new \BadFunctionCallException('Unsupported method call');
    }
}
