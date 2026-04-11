<?php declare(strict_types=1);

namespace KHTools\VPos\Requests;

use KHTools\VPos\Models\Merchant;
use KHTools\VPos\Requests\Traits\MerchantTrait;
use KHTools\VPos\Responses\EchoResponse;
use Symfony\Component\Serializer\Annotation\Ignore;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

class EchoRequest implements RequestInterface
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
        return '/echo';
    }

    #[Ignore]
    public function getResponseClass(): string
    {
        return EchoResponse::class;
    }

    #[Ignore]
    public function getNormalizationContext(): array
    {
        return [
            AbstractNormalizer::CALLBACKS => [
                'merchant' => function (Merchant $value): string {
                    return $value->merchantId;
                },
            ],
        ];
    }
}
