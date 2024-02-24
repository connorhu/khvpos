<?php declare(strict_types=1);

namespace KHTools\VPos\Models;

use KHTools\VPos\Models\Enums\CustomerLoginAuth;

class CustomerLogin
{
    public ?CustomerLoginAuth $auth = null;

    public ?\DateTime $authAt = null;

    public ?string $authData;
}
