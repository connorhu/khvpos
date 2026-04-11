<?php declare(strict_types=1);

namespace KHTools\VPos\Requests;

use KHTools\VPos\Requests\Traits\MerchantTrait;
use Symfony\Component\Serializer\Annotation\Ignore;
use Symfony\Component\Serializer\Annotation\SerializedName;

class OneClickEchoRequest implements RequestInterface
{
    use MerchantTrait;

    #[SerializedName(serializedName: 'origPayId')]
    private ?string $originalPaymentId = null;

    #[Ignore]
    public function getRequestMethod(): string
    {
        return 'POST';
    }

    #[Ignore]
    public function getEndpointPath(): string
    {
        return '/oneclick/echo';
    }

    #[Ignore]
    public function getResponseClass(): string
    {
        throw new \LogicException('Not yet implemented');
    }

    /**
     * @return string|null
     */
    public function getOriginalPaymentId(): ?string
    {
        return $this->originalPaymentId;
    }

    /**
     * @param string|null $originalPaymentId
     */
    public function setOriginalPaymentId(?string $originalPaymentId): void
    {
        $this->originalPaymentId = $originalPaymentId;
    }

    #[Ignore]
    public function getNormalizationContext(): array
    {
        throw new \LogicException('Not yet implemented');
    }
}
