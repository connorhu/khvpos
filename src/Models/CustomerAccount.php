<?php declare(strict_types=1);

namespace KHTools\VPos\Models;

use Symfony\Component\Serializer\Annotation\SerializedName;

class CustomerAccount
{
    public ?\DateTime $createdAt = null;

    public ?\DateTime $changedAt = null;

    public ?\DateTime $passwordChangedAt = null;

    public ?int $orderHistory = null;

    public ?int $paymentsDay = null;

    public ?int $paymentsYear = null;

    #[SerializedName(serializedName: 'oneclickAdds')]
    public ?int $oneClickAdds = null;

    public ?bool $suspicious = null;
}
