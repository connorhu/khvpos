<?php declare(strict_types=1);

namespace KHTools\Tests\Payum;

use KHTools\VPos\Payum\Constants;
use PHPUnit\Framework\TestCase;

class ConstantsTest extends TestCase
{
    public function testPaymentStatusConstantsAreIntegers(): void
    {
        $this->assertSame(1, Constants::STATUS_PENDING);
        $this->assertSame(2, Constants::STATUS_PROCESSING);
        $this->assertSame(3, Constants::STATUS_CANCELLED);
        $this->assertSame(4, Constants::STATUS_CONFIRMED);
        $this->assertSame(5, Constants::STATUS_AUTHORIZED);
        $this->assertSame(6, Constants::STATUS_REJECTED);
        $this->assertSame(7, Constants::STATUS_REVERSED);
        $this->assertSame(8, Constants::STATUS_CLOSED);
        $this->assertSame(0, Constants::RESULT_OK);
        $this->assertSame(150, Constants::RESULT_3DS_IN_PROGRESS);
    }
}
