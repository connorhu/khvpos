<?php declare(strict_types=1);

namespace KHTools\VPos\Models;

class Customer
{
    private ?string $name = null;

    private ?string $email = null;

    private ?string $homePhone = null;

    private ?string $workPhone = null;

    private ?string $mobilePhone = null;

    private ?CustomerAccount $account = null;

    private ?CustomerLogin $login = null;

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     */
    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string|null $email
     */
    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getHomePhone(): ?string
    {
        return $this->homePhone;
    }

    /**
     * @param string|null $homePhone
     */
    public function setHomePhone(?string $homePhone): self
    {
        $this->homePhone = $homePhone;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getWorkPhone(): ?string
    {
        return $this->workPhone;
    }

    /**
     * @param string|null $workPhone
     */
    public function setWorkPhone(?string $workPhone): self
    {
        $this->workPhone = $workPhone;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getMobilePhone(): ?string
    {
        return $this->mobilePhone;
    }

    /**
     * @param string|null $mobilePhone
     */
    public function setMobilePhone(?string $mobilePhone): self
    {
        $this->mobilePhone = $mobilePhone;

        return $this;
    }

    /**
     * @return CustomerAccount|null
     */
    public function getAccount(): ?CustomerAccount
    {
        return $this->account;
    }

    /**
     * @param CustomerAccount|null $account
     */
    public function setAccount(?CustomerAccount $account): self
    {
        $this->account = $account;

        return $this;
    }

    /**
     * @return CustomerLogin|null
     */
    public function getLogin(): ?CustomerLogin
    {
        return $this->login;
    }

    /**
     * @param CustomerLogin|null $login
     */
    public function setLogin(?CustomerLogin $login): self
    {
        $this->login = $login;

        return $this;
    }
}
