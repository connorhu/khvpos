<?php declare(strict_types=1);

namespace KHTools\VPos\Requests;

use KHTools\VPos\Models\Merchant;
use KHTools\VPos\Normalizers\NormalizerResultOrderingHelper;
use KHTools\VPos\Requests\Traits\MerchantTrait;
use KHTools\VPos\Requests\Traits\PaymentIdTrait;
use KHTools\VPos\Responses\EchoResponse;
use KHTools\VPos\Responses\PaymentStatusResponse;
use Symfony\Component\Serializer\Annotation\Ignore;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;

/** @implements RequestInterface<PaymentStatusResponse> */
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
