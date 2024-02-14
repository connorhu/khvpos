<?php declare(strict_types=1);

namespace KHTools\Tests\VPos;

use KHTools\VPos\PaymentResult;
use KHTools\VPos\TransactionInterface;
use PHPUnit\Framework\TestCase;

class PaymentResultTest extends TestCase
{
    public function testResponseInitWithoutEmail()
    {
        $response = 'ACK
0
ELFOGADVA / ENGEDELYEZVE
050309';
        $paymentResult = PaymentResult::initWithResponseString($response);

        $this->assertSame('050309', $paymentResult->getBankLicenceNumber());
        $this->assertSame(TransactionInterface::TRANSACTION_STATUS_ACKNOWLEDGED, $paymentResult->getTransactionStatus());
        $this->assertSame(0, $paymentResult->getResponseCode());
    }

    public function testResponseInitWithEmail()
    {
        $response = 'ACK
0
ELFOGADVA / ENGEDELYEZVE
050309
email@client.tld';
        $paymentResult = PaymentResult::initWithResponseString($response);

        $this->assertSame('050309', $paymentResult->getBankLicenceNumber());
        $this->assertSame(TransactionInterface::TRANSACTION_STATUS_ACKNOWLEDGED, $paymentResult->getTransactionStatus());
        $this->assertSame(0, $paymentResult->getResponseCode());
        $this->assertSame('email@client.tld', $paymentResult->getEmailAddress());
    }

    public function testFailedResponse()
    {
        $response = 'NAK
59
ELUTASITVA, ERVENYTELEN ADAT
00000000';

        $paymentResult = PaymentResult::initWithResponseString($response);

        $this->assertSame('00000000', $paymentResult->getBankLicenceNumber());
        $this->assertSame(TransactionInterface::TRANSACTION_STATUS_FAILED, $paymentResult->getTransactionStatus());
        $this->assertSame(59, $paymentResult->getResponseCode());
        $this->assertSame(null, $paymentResult->getEmailAddress());
    }
}