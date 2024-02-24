<?php declare(strict_types=1);

namespace KHTools\VPos\Models;

use KHTools\VPos\Models\Enums\CustomerLoginAuth;

class CustomerLogin
{
    public ?CustomerLoginAuth $auth = null;

    public ?\DateTime $authAt = null;

    public ?string $authData;

    /**
     * @return CustomerLoginAuth|null
     */
    public function getAuth(): ?CustomerLoginAuth
    {
        return $this->auth;
    }

    /**
     * @param CustomerLoginAuth|null $auth
     */
    public function setAuth(?CustomerLoginAuth $auth): self
    {
        $this->auth = $auth;

        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getAuthAt(): ?\DateTime
    {
        return $this->authAt;
    }

    /**
     * @param \DateTime|null $authAt
     */
    public function setAuthAt(?\DateTime $authAt): self
    {
        $this->authAt = $authAt;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getAuthData(): ?string
    {
        return $this->authData;
    }

    /**
     * @param string|null $authData
     */
    public function setAuthData(?string $authData): self
    {
        $this->authData = $authData;

        return $this;
    }
}
