<?php

namespace KHTools\VPos\Models;

use KHTools\VPos\Models\Enums\HttpMethod;

class Browser
{
    public ?string $url = null;

    public ?HttpMethod $method = null;

    public ?array $vars = null;

    /**
     * @return string|null
     */
    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * @param string|null $url
     */
    public function setUrl(?string $url): self
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return HttpMethod|null
     */
    public function getMethod(): ?HttpMethod
    {
        return $this->method;
    }

    /**
     * @param HttpMethod|null $method
     */
    public function setMethod(?HttpMethod $method): self
    {
        $this->method = $method;

        return $this;
    }

    /**
     * @return array|null
     */
    public function getVars(): ?array
    {
        return $this->vars;
    }

    /**
     * @param array|null $vars
     */
    public function setVars(?array $vars): self
    {
        $this->vars = $vars;

        return $this;
    }
}
