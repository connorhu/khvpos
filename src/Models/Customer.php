<?php declare(strict_types=1);

namespace KHTools\VPos\Models;

class Customer
{
    public ?string $name = null;

    public ?string $email = null;

    public ?string $homePhone = null;

    public ?string $workPhone = null;

    public ?string $mobilePhone = null;

    public ?CustomerAccount $account = null;

    public ?CustomerLogin $login = null;
}
