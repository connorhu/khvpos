<?php declare(strict_types=1);

namespace KHTools\Tests\VPos;

use PHPUnit\Framework\TestCase;

class VPosClientTest extends TestCase
{
    /*private function callPrivateMethod($class, string $methodName, array $args = [])
    {
        $paymentGateway = new \ReflectionClass($class);
        $method = $paymentGateway->getMethod($methodName);
        $method->setAccessible(true);
        
        return $method->invokeArgs($class, $args);
    }
    
    public function testHost(): void
    {
        $httpClient = new Psr18Client();
        $signatureProvider = new SignatureProvider(__DIR__.'/Fixtures/test1_private_key.pem');

        $paymentGateway = new VPosClient(VPosClient::VERSION_V1, 1234, $signatureProvider, true);
        $this->assertSame($this->callPrivateMethod($paymentGateway, 'getEndpointBase'), 'https://pay.sandbox.khpos.hu/pay/v1');

        $paymentGateway = new VPosClient(VPosClient::VERSION_V1, 1234, $signatureProvider, false);
        $this->assertSame($this->callPrivateMethod($paymentGateway, 'getEndpointBase'), 'https://pay.khpos.hu/pay/v1');
    }
    
    /**
     * @dataProvider buildQueryDataProvider
     * /
    public function testBuildQuery(string $languageCode, string $expectedQueryString)
    {
        $httpClient = new Psr18Client();
        $signatureProvider = new SignatureProvider(__DIR__.'/Fixtures/test1_private_key.pem');

        $arguments = new PaymentRequestArguments(PaymentRequestArguments::PAYMENT_PURCHASE_TYPE, 1234, 12313, PaymentRequestArguments::CURRENCY_EUR);
        $arguments->setMerchantId(60000);
        
        $paymentGateway = new VPosClient(VPosClient::VERSION_V1, 60000, $signatureProvider, false);
        
        $this->assertSame($this->callPrivateMethod($paymentGateway, 'buildQuery', [$arguments, $languageCode]), $expectedQueryString);
    }
    
    public function buildQueryDataProvider(): array
    {
        return [
            [VPosClient::LANGUAGE_CODE_HU, 'mid=60000&txid=1234&type=PU&amount=12313&ccy=EUR&sign=9065ac36827e8bb68cd757d6428778083d3f5d289a32cd00aa546df9b54bdf62f69c3883a4b036ba6691bd1cb70f8e7784053285b367242e0d798d0a07c3044149c880b3d9694f7289a160d6c405a48a0a94e1bdab9e615f190437e017d3f9d49ddd636ea0b227a17030c2406393ed9d94f64f4b5302e060e7f81af3a28b0f15'],
            [VPosClient::LANGUAGE_CODE_EN, 'mid=60000&txid=1234&type=PU&amount=12313&ccy=EUR&sign=9065ac36827e8bb68cd757d6428778083d3f5d289a32cd00aa546df9b54bdf62f69c3883a4b036ba6691bd1cb70f8e7784053285b367242e0d798d0a07c3044149c880b3d9694f7289a160d6c405a48a0a94e1bdab9e615f190437e017d3f9d49ddd636ea0b227a17030c2406393ed9d94f64f4b5302e060e7f81af3a28b0f15&lang=EN'],
        ];
    }
    
    /**
     * @dataProvider transactionToPaymentRequestArgumentsDataProvider
     * /
    public function testTransactionToPaymentRequestArguments(TransactionInterface $transaction, string $paymentType, PaymentRequestArguments $requestArgument): void
    {
        $signatureProvider = new SignatureProvider(__DIR__.'/Fixtures/test1_private_key.pem');
        $paymentGateway = new VPosClient(VPosClient::VERSION_V1, 60000, $signatureProvider, false);

        $this->assertEquals($this->callPrivateMethod($paymentGateway, 'transactionToPaymentRequestArguments', [$transaction, $paymentType]), $requestArgument);
    }
    
    public function transactionToPaymentRequestArgumentsDataProvider(): array
    {
        $transaction = new TransactionImplementation();
        $transaction->id = 678;
        $transaction->amount = 123.45;
        $transaction->currency = TransactionInterface::TRANSACTION_EUR_CURRENCY;

        $argument1 = new PaymentRequestArguments(PaymentRequestArguments::PAYMENT_PURCHASE_TYPE, 678, 12345, 'EUR');
        $argument1->setMerchantId(60000);

        $argument2 = new PaymentRequestArguments(PaymentRequestArguments::PAYMENT_RESULT_TYPE, 678);
        $argument2->setMerchantId(60000);

        return [
            [$transaction, PaymentRequestArguments::PAYMENT_PURCHASE_TYPE, $argument1],
            [$transaction, PaymentRequestArguments::PAYMENT_RESULT_TYPE, $argument2],
        ];
    }
    
    /**
     * @dataProvider paymentUrlDataProvider
     * /
    public function testPaymentUrl(TransactionInterface $transaction, string $language, string $url): void
    {
        $signatureProvider = new SignatureProvider(__DIR__.'/Fixtures/test1_private_key.pem');
        $paymentGateway = new VPosClient(VPosClient::VERSION_V1, 60000, $signatureProvider, false);
        
        $this->assertSame($paymentGateway->paymentUrl($transaction, $language), $url);
    }
    
    public function paymentUrlDataProvider(): array
    {
        $transaction = new TransactionImplementation();
        $transaction->id = 678;
        $transaction->amount = 123.45;
        $transaction->currency = TransactionInterface::TRANSACTION_EUR_CURRENCY;

        return [
            [$transaction, VPosClient::LANGUAGE_CODE_HU, 'https://pay.khpos.hu/pay/v1/PGPayment?mid=60000&txid=678&type=PU&amount=12345&ccy=EUR&sign=782ab24d0ea1494c4d1cbb6db768365ec364b8d671cbb15e42ec12b79de98675aa2d00e4667c7973de9933fea346e6ce0459b03e1323d4696d8df7059f9155cccceee71fb87192ffecc6780683e72ac1bda36d1c95160414c3b87fda92242dfc28b8c6ea0022d21f5ec4ee5e5a356a258ac8a6529df08eb179d5ec64a980a5a0'],
            [$transaction, VPosClient::LANGUAGE_CODE_EN, 'https://pay.khpos.hu/pay/v1/PGPayment?mid=60000&txid=678&type=PU&amount=12345&ccy=EUR&sign=782ab24d0ea1494c4d1cbb6db768365ec364b8d671cbb15e42ec12b79de98675aa2d00e4667c7973de9933fea346e6ce0459b03e1323d4696d8df7059f9155cccceee71fb87192ffecc6780683e72ac1bda36d1c95160414c3b87fda92242dfc28b8c6ea0022d21f5ec4ee5e5a356a258ac8a6529df08eb179d5ec64a980a5a0&lang=EN'],
        ];
    }
    
    /**
     * @dataProvider refundUrlDataProvider
     * /
    public function testRefundUrl(TransactionInterface $transaction, string $language, string $url): void
    {
        $signatureProvider = new SignatureProvider(__DIR__.'/Fixtures/test1_private_key.pem');
        $paymentGateway = new VPosClient( VPosClient::VERSION_V1, 60000, $signatureProvider, false);
        
        $this->assertSame($paymentGateway->refundUrl($transaction, null, $language), $url);
    }
    
    public function refundUrlDataProvider(): array
    {
        $transaction = new TransactionImplementation();
        $transaction->id = 678;
        $transaction->amount = 123.45;
        $transaction->currency = TransactionInterface::TRANSACTION_EUR_CURRENCY;

        return [
            [$transaction, VPosClient::LANGUAGE_CODE_HU, 'https://pay.khpos.hu/pay/v1/PGPayment?mid=60000&txid=678&type=RE&amount=12345&ccy=EUR&sign=63187e3ce1800e913761a4f716500ef92a135450a60b8565c53de5fdcd974f06e32bca4d0636b90a8208d61b398d1e1abd65d3e6cfd1b15b115cd001a00ea6f79fa392eaeb74c16380430a88b850ef1a8069bb87eaffe2d1be431eb60a490d1c979d1fc736d97642d2533f4f5c35fc7ec5761d58166d93492d0a695b4cf74adf'],
            [$transaction, VPosClient::LANGUAGE_CODE_EN, 'https://pay.khpos.hu/pay/v1/PGPayment?mid=60000&txid=678&type=RE&amount=12345&ccy=EUR&sign=63187e3ce1800e913761a4f716500ef92a135450a60b8565c53de5fdcd974f06e32bca4d0636b90a8208d61b398d1e1abd65d3e6cfd1b15b115cd001a00ea6f79fa392eaeb74c16380430a88b850ef1a8069bb87eaffe2d1be431eb60a490d1c979d1fc736d97642d2533f4f5c35fc7ec5761d58166d93492d0a695b4cf74adf&lang=EN'],
        ];
    }
    
    public function testPaymentResultCheckUrl(): void
    {
        $transaction = new TransactionImplementation();
        $transaction->id = 678;
        $transaction->amount = 123.45;
        $transaction->currency = TransactionInterface::TRANSACTION_EUR_CURRENCY;

        $signatureProvider = new SignatureProvider(__DIR__.'/Fixtures/test1_private_key.pem');
        $paymentGateway = new VPosClient(VPosClient::VERSION_V1, 60000, $signatureProvider, false);
        
        $this->assertSame($paymentGateway->paymentResultCheckUrl($transaction), 'https://pay.khpos.hu/pay/v1/PGResult?mid=60000&txid=678');
    }*/
}