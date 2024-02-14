<?php

namespace KHTools\VPos\Requests;

use KHTools\VPos\Requests\Traits\MerchantTrait;
use Symfony\Component\Serializer\Annotation\Ignore;

class GooglePayEchoRequest implements RequestInterface
{
    use MerchantTrait;

    #[Ignore]
    public function getRequestMethod(): string
    {
        return 'POST';
    }

    #[Ignore]
    public function getEndpointPath(): string
    {
        return '/googlepay/echo';
    }

    #[Ignore]
    public function getResponseClass(): string
    {
        exit;
        // TODO: Implement getResponseClass() method.
    }
}