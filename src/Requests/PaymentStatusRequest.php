<?php declare(strict_types=1);

namespace KHTools\VPos\Requests;

use KHTools\VPos\Requests\Traits\MerchantTrait;
use KHTools\VPos\Requests\Traits\PaymentIdTrait;
use KHTools\VPos\Responses\EchoResponse;
use KHTools\VPos\Responses\PaymentStatusResponse;
use Symfony\Component\Serializer\Annotation\Ignore;

class PaymentStatusRequest implements RequestInterface
{
    use MerchantTrait;
    use PaymentIdTrait;

    #[Ignore]
    public function getRequestMethod(): string
    {
        return 'GET';
    }

    #[Ignore]
    public function getEndpointPath(): string
    {
        return '/payment/status/{merchantId}/{payId}/{dttm}/{signature}';
    }

    #[Ignore]
    public function getResponseClass(): string
    {
        return PaymentStatusResponse::class;
    }
}