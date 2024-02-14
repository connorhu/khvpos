<?php

namespace KHTools\VPos\Entities;

use KHTools\VPos\Entities\Enums\HttpMethod;

class Browser
{
    public ?string $url = null;

    public ?HttpMethod $method = null;

    public ?array $vars = null;
}