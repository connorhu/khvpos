<?php declare(strict_types=1);

namespace KHTools\VPos\Requests\Traits;

use Symfony\Component\Serializer\Annotation\SerializedName;

trait PaymentIdTrait
{
    #[SerializedName(serializedName: 'payId')]
    private string $paymentId;

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
}