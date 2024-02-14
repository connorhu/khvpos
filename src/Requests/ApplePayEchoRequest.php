<?php

namespace KHTools\VPos\Requests;

use KHTools\VPos\Requests\Traits\MerchantTrait;
use Symfony\Component\Serializer\Annotation\Ignore;

class ApplePayEchoRequest implements RequestInterface
{
    use MerchantTrait;

    private ?string $clientIp = null;

    private ?string $payload = null;

    private bool $sdkUsed = false;

    #[Ignore]
    public function getRequestMethod(): string
    {
        return 'POST';
    }

    #[Ignore]
    public function getEndpointPath(): string
    {
        return '/applepay/echo';
    }

    #[Ignore]
    public function getResponseClass(): string
    {
        exit;
        // TODO: Implement getResponseClass() method.
    }
}