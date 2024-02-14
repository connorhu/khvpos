<?php declare(strict_types=1);

namespace KHTools\VPos\Requests;

use KHTools\VPos\Requests\Traits\MerchantTrait;
use KHTools\VPos\Requests\Traits\PaymentIdTrait;
use KHTools\VPos\Responses\EchoResponse;
use KHTools\VPos\Responses\PaymentRefundResponse;
use Symfony\Component\Serializer\Annotation\Ignore;

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
        $this->amount = (int) round($amount * 100);
    }
}