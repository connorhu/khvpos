<?php

namespace KHTools\VPos\Responses;

use KHTools\VPos\Responses\Traits\CommonResponseTrait;
use Symfony\Component\Serializer\Annotation\Ignore;

class PaymentStatusResponse implements ResponseInterface
{
    use CommonResponseTrait;

    private string $paymentId;

    private ?int $paymentStatus = null;

    private ?string $authorizationCode = null;

    private ?array $statusDetail = [];

    /**
     * @TODO implement
     */
    #[Ignore]
    private $actions;

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
     * @return int|null
     */
    public function getPaymentStatus(): ?int
    {
        return $this->paymentStatus;
    }

    /**
     * @param int|null $paymentStatus
     */
    public function setPaymentStatus(?int $paymentStatus): void
    {
        $this->paymentStatus = $paymentStatus;
    }

    /**
     * @return string|null
     */
    public function getAuthorizationCode(): ?string
    {
        return $this->authorizationCode;
    }

    /**
     * @param string|null $authorizationCode
     */
    public function setAuthorizationCode(?string $authorizationCode): void
    {
        $this->authorizationCode = $authorizationCode;
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