<?php declare(strict_types=1);

namespace KHTools\Tests\Responses;

use KHTools\VPos\Responses\Traits\PaymentActionResponseTrait;
use PHPUnit\Framework\TestCase;

class PaymentActionResponseTraitTest extends TestCase
{
	private object $subject;

	protected function setUp(): void
	{
		$this->subject = new class {
			use PaymentActionResponseTrait;
		};
	}

	public function testPaymentIdGetterSetter(): void
	{
		$this->assertNull($this->subject->getPaymentId());
		$this->subject->setPaymentId('pay001');
		$this->assertSame('pay001', $this->subject->getPaymentId());
	}

	public function testPaymentStatusGetterSetter(): void
	{
		$this->assertNull($this->subject->getPaymentStatus());
		$this->subject->setPaymentStatus(2);
		$this->assertSame(2, $this->subject->getPaymentStatus());
	}

	public function testStatusDetailGetterSetter(): void
	{
		$this->assertNull($this->subject->getStatusDetail());
		$this->subject->setStatusDetail('detail string');
		$this->assertSame('detail string', $this->subject->getStatusDetail());
	}

	public function testAuthenticateActionGetterSetter(): void
	{
		$this->assertNull($this->subject->getAuthenticateAction());
		$this->subject->setAuthenticateAction(['redirectUrl' => 'https://example.com']);
		$this->assertSame(['redirectUrl' => 'https://example.com'], $this->subject->getAuthenticateAction());
	}
}
