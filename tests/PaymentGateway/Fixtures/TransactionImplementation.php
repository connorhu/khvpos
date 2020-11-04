<?php

namespace KHBankTools\Tests\PaymentGateway\Fixtures;

use KHBankTools\PaymentGateway\TransactionInterface;

class TransactionImplementation implements TransactionInterface
{
    public $id;
    
    public $amount;
    
    public $currency = TransactionInterface::TRANSACTION_HUF_CURRENCY;

    public function getId(): int
    {
        return $this->id;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getCurrency(): int
    {
        return $this->currency;
    }
    
    public function getStatus(): int
    {
        return 0;
    }
}