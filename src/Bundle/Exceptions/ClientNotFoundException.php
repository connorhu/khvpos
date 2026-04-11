<?php declare(strict_types=1);

namespace KHTools\VPos\Bundle\Exceptions;

class ClientNotFoundException extends \RuntimeException
{
    public function __construct(string $isoCurrency)
    {
        parent::__construct(sprintf('VPos Client not found with currency: "%s"', $isoCurrency));
    }
}
