<?php declare(strict_types=1);

namespace KHTools\VPos\Entities;

use KHTools\VPos\Entities\Enums\CustomerLoginAuth;

class CustomerLogin
{
    public ?CustomerLoginAuth $auth = null;

    public ?\DateTime $authAt = null;

    public ?string $authData;
}