<?php declare(strict_types=1);

namespace KHTools\VPos\Models;

use KHTools\VPos\Models\Enums\CustomerLoginAuth;

class CustomerLogin
{
    private ?CustomerLoginAuth $auth = null;

    private ?\DateTime $authAt = null;

    private ?string $authData = null;

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
    public function setAuth(?CustomerLoginAuth $auth): void
    {
        $this->auth = $auth;
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
    public function setAuthAt(?\DateTime $authAt): void
    {
        $this->authAt = $authAt;
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
    public function setAuthData(?string $authData): void
    {
        $this->authData = $authData;
    }
}
