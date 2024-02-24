<?php declare(strict_types=1);

namespace KHTools\VPos\Models;

use Symfony\Component\Serializer\Annotation\SerializedName;

class CustomerAccount
{
    public ?\DateTimeImmutable $createdAt = null;

    public ?\DateTimeImmutable $changedAt = null;

    public ?\DateTime $passwordChangedAt = null;

    public ?int $orderHistory = null;

    public ?int $paymentsDay = null;

    public ?int $paymentsYear = null;

    #[SerializedName(serializedName: 'oneclickAdds')]
    public ?int $oneClickAdds = null;

    public ?bool $suspicious = null;

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
    public function setCreatedAt(?\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
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
    public function setChangedAt(?\DateTimeImmutable $changedAt): self
    {
        $this->changedAt = $changedAt;

        return $this;
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
    public function setPasswordChangedAt(?\DateTime $passwordChangedAt): self
    {
        $this->passwordChangedAt = $passwordChangedAt;

        return $this;
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
    public function setOrderHistory(?int $orderHistory): self
    {
        $this->orderHistory = $orderHistory;

        return $this;
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
    public function setPaymentsDay(?int $paymentsDay): self
    {
        $this->paymentsDay = $paymentsDay;

        return $this;
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
    public function setPaymentsYear(?int $paymentsYear): self
    {
        $this->paymentsYear = $paymentsYear;

        return $this;
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
    public function setOneClickAdds(?int $oneClickAdds): self
    {
        $this->oneClickAdds = $oneClickAdds;

        return $this;
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
    public function setSuspicious(?bool $suspicious): self
    {
        $this->suspicious = $suspicious;

        return $this;
    }
}
