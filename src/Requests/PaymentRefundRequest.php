<?php declare(strict_types=1);

namespace KHTools\VPos\Requests;

use KHTools\VPos\Models\Merchant;
use KHTools\VPos\Normalizers\NormalizerResultOrderingHelper;
use KHTools\VPos\Requests\Traits\MerchantTrait;
use KHTools\VPos\Requests\Traits\PaymentIdTrait;
use KHTools\VPos\Responses\EchoResponse;
use KHTools\VPos\Responses\PaymentRefundResponse;
use Symfony\Component\Serializer\Annotation\Ignore;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;

class PaymentRefundRequest implements RequestInterface
{
    use MerchantTrait;
    use PaymentIdTrait;

    private ?int $amount = null;

    #[Ignore]
    public function getRequestMethod(): string
    {
        return 'PUT';
    }

    #[Ignore]
    public function getEndpointPath(): string
    {
        return '/payment/refund';
    }

    #[Ignore]
    public function getResponseClass(): string
    {
        return PaymentRefundResponse::class;
    }

    /**
     * @return float|null
     */
    public function getAmount(): ?float
    {
        return $this->amount / 100;
    }

    public function getRawAmount(): ?int
    {
        return $this->amount;
    }

    /**
     * @param float|null $amount
     */
    public function setAmount(?float $amount): void
    {
        $this->amount = (int) \bcmul(number_format((float) $amount, 2, '.', ''), '100');
    }

    #[Ignore]
    public function getNormalizationContext(): array
    {
        return [
            AbstractNormalizer::CALLBACKS => [
                'merchant' => function (Merchant $value): string {
                    return $value->merchantId;
                },
                'amount' => function (mixed $value, PaymentRefundRequest $object): ?int {
                    return $object->getRawAmount();
                },
            ],
            AbstractNormalizer::IGNORED_ATTRIBUTES => ['rawAmount'],
            AbstractObjectNormalizer::SKIP_NULL_VALUES => true,
            NormalizerResultOrderingHelper::ORDER => [
                'merchantId',
                'payId',
                'dttm',
                'amount',
            ],
        ];
    }
}
