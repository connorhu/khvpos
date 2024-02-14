<?php

namespace KHTools\VPos\Responses;

use KHTools\VPos\Entities\Authenticate;
use KHTools\VPos\Responses\Traits\CommonResponseTrait;
use Symfony\Component\Serializer\Annotation\SerializedName;

class PaymentStatusResponse implements ResponseInterface
{
    use CommonResponseTrait;

    #[SerializedName(serializedName: 'payId')]
    private string $paymentId;

    private ?int $paymentStatus = null;

    #[SerializedName(serializedName: 'authCode')]
    private ?string $authorizationCode = null;

    private ?string $statusDetail = null;

    private ?Authenticate $authenticateAction = null;

    // private ?Fingerprint $fingerprintAction = null;

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
     * @return string|null
     */
    public function getStatusDetail(): ?string
    {
        return $this->statusDetail;
    }

    /**
     * @param string|null $statusDetail
     */
    public function setStatusDetail(?string $statusDetail): void
    {
        $this->statusDetail = $statusDetail;
    }

    /**
     * @return Authenticate|null
     */
    public function getAuthenticateAction(): ?Authenticate
    {
        return $this->authenticateAction;
    }

    /**
     * @param Authenticate|null $authenticateAction
     */
    public function setAuthenticateAction(?Authenticate $authenticateAction): void
    {
        $this->authenticateAction = $authenticateAction;
    }
}