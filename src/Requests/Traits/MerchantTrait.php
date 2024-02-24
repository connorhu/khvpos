<?php declare(strict_types=1);

namespace KHTools\VPos\Requests\Traits;

use KHTools\VPos\Models\Merchant;
use Symfony\Component\Serializer\Annotation\SerializedName;

trait MerchantTrait
{
    #[SerializedName(serializedName: 'merchantId')]
    private Merchant $merchant;

    /**
     * @return Merchant
     */
    public function getMerchant(): Merchant
    {
        return $this->merchant;
    }

    /**
     * @param Merchant $merchant
     */
    public function setMerchant(Merchant $merchant): void
    {
        $this->merchant = $merchant;
    }
}
