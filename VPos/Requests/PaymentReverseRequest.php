<?php declare(strict_types=1);

namespace KHTools\VPos\Requests;

use KHTools\VPos\Requests\Traits\MerchantTrait;
use KHTools\VPos\Requests\Traits\PaymentIdTrait;
use KHTools\VPos\Responses\EchoResponse;
use Symfony\Component\Serializer\Annotation\Ignore;

class PaymentReverseRequest implements RequestInterface
{
    use MerchantTrait;
    use PaymentIdTrait;

    #[Ignore]
    public function getRequestMethod(): string
    {
        return 'PUT';
    }

    #[Ignore]
    public function getEndpointPath(): string
    {
        return '/payment/reverse';
    }

    #[Ignore]
    public function getResponseClass(): string
    {
        exit;
        return EchoResponse::class;
    }
}