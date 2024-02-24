<?php

namespace KHTools\Tests\Normalizers;

use KHTools\VPos\Entities\CartItem;
use KHTools\VPos\Entities\Customer;
use KHTools\VPos\Entities\CustomerAccount;
use KHTools\VPos\Entities\CustomerLogin;
use KHTools\VPos\Entities\Enums\Currency;
use KHTools\VPos\Entities\Enums\CustomerLoginAuth;
use KHTools\VPos\Entities\Enums\Language;
use KHTools\VPos\Entities\Enums\PaymentMethod;
use KHTools\VPos\Entities\Enums\PaymentOperation;
use KHTools\VPos\Entities\Enums\HttpMethod;
use KHTools\VPos\Entities\Merchant;
use KHTools\VPos\Normalizers\CartItemNormalizer;
use KHTools\VPos\Normalizers\EnumNormalizer;
use KHTools\VPos\Normalizers\RequestNormalizer;
use KHTools\VPos\Requests\PaymentCloseRequest;
use KHTools\VPos\Requests\PaymentInitRequest;
use KHTools\VPos\Requests\PaymentProcessRequest;
use KHTools\VPos\Requests\PaymentRefundRequest;
use KHTools\VPos\Requests\PaymentReverseRequest;
use KHTools\VPos\Requests\PaymentStatusRequest;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Mapping\Loader\AttributeLoader;
use Symfony\Component\Serializer\NameConverter\MetadataAwareNameConverter;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * @todo add tests value/key ordering
 */
class RequestNormalizerTest extends TestCase
{
    private NormalizerInterface $normalizer;

    protected function setUp(): void
    {
        $loader = class_exists(AttributeLoader::class) ? new AttributeLoader() : new AnnotationLoader();
        $classMetadataFactory = new ClassMetadataFactory($loader);
        $metadataAwareNameConverter = new MetadataAwareNameConverter($classMetadataFactory);

        $dateTimeNormalizer = new DateTimeNormalizer();
        $objectNormalizer = new ObjectNormalizer($classMetadataFactory, $metadataAwareNameConverter);

        $this->normalizer = new Serializer([
            new RequestNormalizer($objectNormalizer),
            new CartItemNormalizer($objectNormalizer),
            new EnumNormalizer(),
            $dateTimeNormalizer,
            $objectNormalizer,
        ]);
    }

    #[DataProvider(methodName: 'paymentInitDataProvider')]
    public function testPaymentInit(PaymentInitRequest $paymentInit, array $expected)
    {
        $result = $this->normalizer->normalize($paymentInit);

        foreach ($expected as $key => $value) {
            $this->assertSame($value, $result[$key]);
        }
    }

    public static function paymentInitDataProvider(): \Generator
    {
        $paymentInit = new PaymentInitRequest();
        $merchant = new Merchant();
        $merchant->merchantId = 'abc123';
        $paymentInit->setMerchant($merchant);
        yield [$paymentInit, [
            'merchantId' => 'abc123',
        ]];

        $paymentInit = new PaymentInitRequest();
        $paymentInit->setClosePayment(false);
        $paymentInit->setMerchant($merchant);
        yield [$paymentInit, [
            'closePayment' => false,
        ]];

        $paymentInit = new PaymentInitRequest();
        $paymentInit->setClosePayment(true);
        $paymentInit->setMerchant($merchant);
        yield [$paymentInit, [
            'closePayment' => true,
        ]];

        $paymentInit = new PaymentInitRequest();
        $paymentInit->setOrderNumber('1234');
        yield [$paymentInit, [
            'orderNo' => '1234',
        ]];

        $paymentInit = new PaymentInitRequest();
        $paymentInit->setPaymentOperation(PaymentOperation::Payment);
        yield [$paymentInit, [
            'payOperation' => 'payment',
        ]];

        $paymentInit = new PaymentInitRequest();
        $paymentInit->setPaymentMethod(PaymentMethod::LowValuePayment);
        yield [$paymentInit, [
            'payMethod' => 'card#LVP',
        ]];

        $paymentInit = new PaymentInitRequest();
        $paymentInit->setTotalAmount(1234);
        yield [$paymentInit, [
            'totalAmount' => 123400,
        ]];

        $paymentInit = new PaymentInitRequest();
        $paymentInit->setTotalAmount(1234.55);
        yield [$paymentInit, [
            'totalAmount' => 123455,
        ]];

        // round tests
        $paymentInit = new PaymentInitRequest();
        $paymentInit->setTotalAmount(1234.552);
        yield [$paymentInit, [
            'totalAmount' => 123455,
        ]];

        $paymentInit = new PaymentInitRequest();
        $paymentInit->setTotalAmount(1234.586);
        yield [$paymentInit, [
            'totalAmount' => 123459,
        ]];

        $paymentInit = new PaymentInitRequest();
        $paymentInit->setCurrency(Currency::EUR);
        yield [$paymentInit, [
            'currency' => 'EUR',
        ]];

        $paymentInit = new PaymentInitRequest();
        $paymentInit->setReturnUrl('https://not.exist.tld');
        yield [$paymentInit, [
            'returnUrl' => 'https://not.exist.tld',
        ]];

        $paymentInit = new PaymentInitRequest();
        $paymentInit->setReturnMethod(HttpMethod::Post);
        yield [$paymentInit, [
            'returnMethod' => 'POST',
        ]];

        $paymentInit = new PaymentInitRequest();
        $paymentInit->setLanguage(Language::CS);
        yield [$paymentInit, [
            'language' => 'cs',
        ]];

        $paymentInit = new PaymentInitRequest();
        $paymentInit->setTtl(1800);
        yield [$paymentInit, [
            'ttlSec' => 1800,
        ]];

        // empty and send by default
        $paymentInit = new PaymentInitRequest();
        yield [$paymentInit, [
            'cart' => [],
        ]];

        $cartItem = new CartItem();
        $cartItem->setName('name of the item');
        $cartItem->setQuantity(111);
        $cartItem->setAmount(12345);
        $cartItem->setDescription('description of the item');
        $paymentInit = new PaymentInitRequest();
        $paymentInit->addCartItem($cartItem);
        yield [$paymentInit, [
            'cart' => [
                [
                    'name' => 'name of the item',
                    'quantity' => 111,
                    'amount' => 1234500,
                    'description' => 'description of the item',
                ]
            ],
        ]];

        $customer = new Customer();
        $customer->account = new CustomerAccount();
        $customer->account->changedAt = new \DateTime('2023-01-01T04:05:06+00:00');
        $customer->login = new CustomerLogin();
        $customer->login->auth = CustomerLoginAuth::Api;
        $customer->name = 'name of the customer';
        $paymentInit = new PaymentInitRequest();
        $paymentInit->setCustomer($customer);
        yield [$paymentInit, [
            'customer' => [
                'name' => 'name of the customer',
                'account' => [
                    'changedAt' => '2023-01-01T04:05:06+00:00',
                ],
                'login' => [
                    'auth' => 'api',
                ],
            ],
        ]];
    }

    #[DataProvider(methodName: 'simplePaymentRequestsDataProvider')]
    public function testSimplePaymentRequests(PaymentStatusRequest|PaymentProcessRequest|PaymentReverseRequest $request, array $expected)
    {
        $result = $this->normalizer->normalize($request);

        $this->assertNotNull($result['dttm']);
        unset($result['dttm']);

        $this->assertSame($expected, $result);
    }

    public static function simplePaymentRequestsDataProvider(): \Generator
    {
        $request = new PaymentStatusRequest();
        yield [$request, []];

        $request = new PaymentStatusRequest();
        $request->setPaymentId('abc123');
        yield [$request, [
            'payId' => 'abc123',
        ]];

        $request = new PaymentProcessRequest();
        yield [$request, []];

        $request = new PaymentProcessRequest();
        $request->setPaymentId('abc123');
        yield [$request, [
            'payId' => 'abc123',
        ]];

        $request = new PaymentReverseRequest();
        yield [$request, []];

        $request = new PaymentReverseRequest();
        $request->setPaymentId('abc123');
        yield [$request, [
            'payId' => 'abc123',
        ]];
    }

    #[DataProvider(methodName: 'paymentCloseRequestDataProvider')]
    public function testPaymentCloseRequest(PaymentCloseRequest $request, array $expected)
    {
        $result = $this->normalizer->normalize($request);

        $this->assertNotNull($result['dttm']);
        unset($result['dttm']);

        $this->assertSame($expected, $result);
    }

    public static function paymentCloseRequestDataProvider(): \Generator
    {
        $request = new PaymentCloseRequest();
        yield [$request, []];

        $request = new PaymentCloseRequest();
        $request->setTotalAmount(1000);
        $request->setPaymentId('abc123');
        yield [$request, [
            'payId' => 'abc123',
            'totalAmount' => 100000,
        ]];
    }

    #[DataProvider(methodName: 'paymentRefundRequestDataProvider')]
    public function testPaymentRefundRequest(PaymentRefundRequest $request, array $expected)
    {
        $result = $this->normalizer->normalize($request);

        $this->assertNotNull($result['dttm']);
        unset($result['dttm']);
        $this->assertSame($expected, $result);
    }

    public static function paymentRefundRequestDataProvider(): \Generator
    {
        $request = new PaymentRefundRequest();
        yield [$request, []];

        $request = new PaymentRefundRequest();
        $request->setAmount(1000);
        $request->setPaymentId('abc123');
        yield [$request, [
            'payId' => 'abc123',
            'amount' => 100000,
        ]];
    }
}
