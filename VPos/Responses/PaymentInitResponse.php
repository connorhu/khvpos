<?php

namespace KHTools\VPos\Responses;

use KHTools\VPos\Responses\Traits\CommonResponseTrait;
use Symfony\Component\Serializer\Annotation\SerializedName;

class PaymentInitResponse implements ResponseInterface
{
    use CommonResponseTrait;

    #[SerializedName(serializedName: 'payId')]
    private string $paymentId;

    private int $paymentStatus;

    private ?array $statusDetail = [];

    /**
     * @return string
     */
    public function getPaymentId(): string
    {
        return $this->paymentId;
    }

    /**
     * @param string $paymentId
     */
    public function setPaymentId(string $paymentId): void
    {
        $this->paymentId = $paymentId;
    }

    /**
     * @return int
     */
    public function getPaymentStatus(): int
    {
        return $this->paymentStatus;
    }

    /**
     * @param int $paymentStatus
     */
    public function setPaymentStatus(int $paymentStatus): void
    {
        $this->paymentStatus = $paymentStatus;
    }

    /**
     * @return array|null
     */
    public function getStatusDetail(): ?array
    {
        return $this->statusDetail;
    }

    /**
     * @param array|null $statusDetail
     */
    public function setStatusDetail(?array $statusDetail): void
    {
        $this->statusDetail = $statusDetail;
    }
}