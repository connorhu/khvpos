<?php declare(strict_types=1);

namespace KHTools\VPos\Requests;

use KHTools\VPos\Requests\Traits\MerchantTrait;
use KHTools\VPos\Requests\Traits\PaymentIdTrait;
use KHTools\VPos\Responses\EchoResponse;
use KHTools\VPos\Responses\PaymentCloseResponse;
use Symfony\Component\Serializer\Annotation\Ignore;

class PaymentCloseRequest implements RequestInterface
{
    use MerchantTrait;
    use PaymentIdTrait;

    private ?int $totalAmount = null;

    #[Ignore]
    public function getRequestMethod(): string
    {
        return 'PUT';
    }

    #[Ignore]
    public function getEndpointPath(): string
    {
        return '/payment/close';
    }

    #[Ignore]
    public function getResponseClass(): string
    {
        return PaymentCloseResponse::class;
    }

    /**
     * @return float|null
     */
    public function getTotalAmount(): ?float
    {
        return $this->totalAmount / 100;
    }

    public function getRawTotalAmount(): ?int
    {
        return $this->totalAmount;
    }

    /**
     * @param int|null $totalAmount
     */
    public function setTotalAmount(?int $totalAmount): void
    {
        $this->totalAmount = (int) round($totalAmount * 100);
    }
}