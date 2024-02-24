<?php declare(strict_types=1);

namespace KHTools\VPos\Models;

class Merchant
{
    public string $merchantId;

    /**
     * @return string
     */
    public function getMerchantId(): string
    {
        return $this->merchantId;
    }

    /**
     * @param string $merchantId
     */
    public function setMerchantId(string $merchantId): static
    {
        $this->merchantId = $merchantId;

        return $this;
    }
}
