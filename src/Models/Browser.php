<?php

namespace KHTools\VPos\Models;

use KHTools\VPos\Models\Enums\HttpMethod;

class Browser
{
    public ?string $url = null;

    public ?HttpMethod $method = null;

    public ?array $vars = null;
}
