<?php declare(strict_types=1);

namespace KHTools\VPos\Requests;

use KHTools\VPos\Models\Merchant;
use KHTools\VPos\Normalizers\NormalizerResultOrderingHelper;
use KHTools\VPos\Requests\Traits\MerchantTrait;
use KHTools\VPos\Requests\Traits\PaymentIdTrait;
use KHTools\VPos\Responses\PaymentReverseResponse;
use Symfony\Component\Serializer\Annotation\Ignore;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;

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
        return PaymentReverseResponse::class;
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
            AbstractObjectNormalizer::SKIP_NULL_VALUES => true,
            NormalizerResultOrderingHelper::ORDER => [
                'merchantId',
                'payId',
                'dttm',
            ],
        ];
    }
}