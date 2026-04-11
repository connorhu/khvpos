<?php declare(strict_types=1);

namespace KHTools\VPos\Models;

use Symfony\Component\Serializer\Annotation\SerializedName;

class CustomerAccount
{
    private ?\DateTimeImmutable $createdAt = null;

    private ?\DateTimeImmutable $changedAt = null;

    private ?\DateTime $passwordChangedAt = null;

    private ?int $orderHistory = null;

    private ?int $paymentsDay = null;

    private ?int $paymentsYear = null;

    #[SerializedName(serializedName: 'oneclickAdds')]
    private ?int $oneClickAdds = null;

    private ?bool $suspicious = null;

    /**
     * @return \DateTimeImmutable|null
     */
    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTimeImmutable|null $createdAt
     */
    public function setCreatedAt(?\DateTimeImmutable $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return \DateTimeImmutable|null
     */
    public function getChangedAt(): ?\DateTimeImmutable
    {
        return $this->changedAt;
    }

    /**
     * @param \DateTimeImmutable|null $changedAt
     */
    public function setChangedAt(?\DateTimeImmutable $changedAt): void
    {
        $this->changedAt = $changedAt;
    }

    /**
     * @return \DateTime|null
     */
    public function getPasswordChangedAt(): ?\DateTime
    {
        return $this->passwordChangedAt;
    }

    /**
     * @param \DateTime|null $passwordChangedAt
     */
    public function setPasswordChangedAt(?\DateTime $passwordChangedAt): void
    {
        $this->passwordChangedAt = $passwordChangedAt;
    }

    /**
     * @return int|null
     */
    public function getOrderHistory(): ?int
    {
        return $this->orderHistory;
    }

    /**
     * @param int|null $orderHistory
     */
    public function setOrderHistory(?int $orderHistory): void
    {
        $this->orderHistory = $orderHistory;
    }

    /**
     * @return int|null
     */
    public function getPaymentsDay(): ?int
    {
        return $this->paymentsDay;
    }

    /**
     * @param int|null $paymentsDay
     */
    public function setPaymentsDay(?int $paymentsDay): void
    {
        $this->paymentsDay = $paymentsDay;
    }

    /**
     * @return int|null
     */
    public function getPaymentsYear(): ?int
    {
        return $this->paymentsYear;
    }

    /**
     * @param int|null $paymentsYear
     */
    public function setPaymentsYear(?int $paymentsYear): void
    {
        $this->paymentsYear = $paymentsYear;
    }

    /**
     * @return int|null
     */
    public function getOneClickAdds(): ?int
    {
        return $this->oneClickAdds;
    }

    /**
     * @param int|null $oneClickAdds
     */
    public function setOneClickAdds(?int $oneClickAdds): void
    {
        $this->oneClickAdds = $oneClickAdds;
    }

    /**
     * @return bool|null
     */
    public function getSuspicious(): ?bool
    {
        return $this->suspicious;
    }

    /**
     * @param bool|null $suspicious
     */
    public function setSuspicious(?bool $suspicious): void
    {
        $this->suspicious = $suspicious;
    }
}
