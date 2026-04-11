<?php declare(strict_types=1);

namespace KHTools\Tests\Payum\Action;

use KHTools\VPos\Payum\Action\ConvertPaymentAction;
use Payum\Core\Model\Payment;
use Payum\Core\Request\Convert;
use PHPUnit\Framework\TestCase;

class ConvertPaymentActionTest extends TestCase
{
    private ConvertPaymentAction $action;

    protected function setUp(): void
    {
        $this->action = new ConvertPaymentAction();
    }

    public function testSupportConvertWithPaymentModel(): void
    {
        $convert = new Convert(new Payment(), 'array');
        $this->assertTrue($this->action->supports($convert));
    }

    public function testDoesNotSupportConvertWithNonPaymentSource(): void
    {
        $convert = new Convert(new \stdClass(), 'array');
        $this->assertFalse($this->action->supports($convert));
    }

    public function testDoesNotSupportConvertToNonArray(): void
    {
        $convert = new Convert(new Payment(), 'json');
        $this->assertFalse($this->action->supports($convert));
    }

    public function testMapsPaymentFieldsToDetailsArray(): void
    {
        $payment = new Payment();
        $payment->setNumber('ORDER-001');
        $payment->setTotalAmount(10000); // 100.00 HUF in fillér
        $payment->setCurrencyCode('HUF');
        $payment->setDetails([
            'return_url'    => 'https://example.com/return',
            'return_method' => 'POST',
            'merchant_id'   => 'M123',
        ]);

        $convert = new Convert($payment, 'array');
        $this->action->execute($convert);

        $result = $convert->getResult();
        $this->assertSame('ORDER-001', $result['order_number']);
        $this->assertSame(10000, $result['total_amount']);
        $this->assertSame('HUF', $result['currency']);
        $this->assertSame('https://example.com/return', $result['return_url']);
        $this->assertSame('POST', $result['return_method']);
        $this->assertSame('M123', $result['merchant_id']);
    }

    public function testDefaultsReturnMethodToPost(): void
    {
        $payment = new Payment();
        $payment->setNumber('ORDER-002');
        $payment->setTotalAmount(500);
        $payment->setCurrencyCode('HUF');
        $payment->setDetails(['return_url' => 'https://example.com/return']);

        $convert = new Convert($payment, 'array');
        $this->action->execute($convert);

        $this->assertSame('POST', $convert->getResult()['return_method']);
    }
}
