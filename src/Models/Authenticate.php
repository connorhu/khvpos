<?php

namespace KHTools\VPos\Models;

class Authenticate
{
    public ?Browser $browserChallenge = null;

    public ?Sdk $sdkChallenge = null;

    /**
     * @return Browser|null
     */
    public function getBrowserChallenge(): ?Browser
    {
        return $this->browserChallenge;
    }

    /**
     * @param Browser|null $browserChallenge
     */
    public function setBrowserChallenge(?Browser $browserChallenge): self
    {
        $this->browserChallenge = $browserChallenge;

        return $this;
    }

    /**
     * @return Sdk|null
     */
    public function getSdkChallenge(): ?Sdk
    {
        return $this->sdkChallenge;
    }

    /**
     * @param Sdk|null $sdkChallenge
     */
    public function setSdkChallenge(?Sdk $sdkChallenge): self
    {
        $this->sdkChallenge = $sdkChallenge;

        return $this;
    }
}
