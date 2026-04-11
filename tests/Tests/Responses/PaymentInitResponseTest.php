<?php declare(strict_types=1);

namespace KHTools\Tests\Responses;

use KHTools\VPos\Responses\PaymentInitResponse;
use PHPUnit\Framework\TestCase;

class PaymentInitResponseTest extends TestCase
{
    public function testCustomerCodeIsStoredAndRetrieved(): void
    {
        $response = new PaymentInitResponse();
        $response->setCustomerCode('CUST001');
        $this->assertSame('CUST001', $response->getCustomerCode());
    }

    public function testCustomerCodeDefaultsToNull(): void
    {
        $response = new PaymentInitResponse();
        $this->assertNull($response->getCustomerCode());
    }
}
