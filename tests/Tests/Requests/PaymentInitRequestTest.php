<?php

namespace KHTools\Tests\Requests;

use KHTools\VPos\Requests\PaymentInitRequest;
use PHPUnit\Framework\TestCase;

class PaymentInitRequestTest extends TestCase
{
    public function testTotalAmount(): void
    {
        $request = new PaymentInitRequest();
        $request->setTotalAmount(100);
        self::assertSame(100.0, $request->getTotalAmount());
        self::assertSame(10000, $request->getRawTotalAmount());

        $request = new PaymentInitRequest();
        $request->setTotalAmount(100.0);
        self::assertSame(100.0, $request->getTotalAmount());
        self::assertSame(10000, $request->getRawTotalAmount());

        $request = new PaymentInitRequest();
        $request->setTotalAmount(100.30);
        self::assertSame(100.30, $request->getTotalAmount());
        self::assertSame(10030, $request->getRawTotalAmount());

        $request = new PaymentInitRequest();
        $request->setTotalAmount(314.96);
        self::assertSame(314.96, $request->getTotalAmount());
        self::assertSame(31496, $request->getRawTotalAmount());
    }
}
