<?php declare(strict_types=1);

namespace KHTools\VPos\Requests;

use KHTools\VPos\Models\CartItem;
use KHTools\VPos\Models\Customer;
use KHTools\VPos\Models\Enums\Currency;
use KHTools\VPos\Models\Enums\Language;
use KHTools\VPos\Models\Enums\PaymentMethod;
use KHTools\VPos\Models\Enums\PaymentOperation;
use KHTools\VPos\Models\Enums\HttpMethod;
use KHTools\VPos\Models\Order;
use KHTools\VPos\Requests\Traits\MerchantTrait;
use KHTools\VPos\Responses\PaymentInitResponse;
use Symfony\Component\Serializer\Annotation\Ignore;
use Symfony\Component\Serializer\Annotation\SerializedName;

class PaymentInitRequest implements RequestInterface
{
    use MerchantTrait;

    #[SerializedName(serializedName: 'orderNo')]
    private string $orderNumber;

    #[SerializedName(serializedName: 'payOperation')]
    private ?PaymentOperation $paymentOperation = null;

    #[SerializedName(serializedName: 'payMethod')]
    private ?PaymentMethod $paymentMethod = null;

    private int $totalAmount;

    private Currency $currency;

    private ?bool $closePayment = null;

    private string $returnUrl;

    private HttpMethod $returnMethod;

    /**
     * @var array<int, CartItem>
     */
    #[SerializedName(serializedName: 'cart')]
    private array $cartItems = [];

    private ?Customer $customer = null;

    private ?Order $order = null;

    private ?string $merchantData = null;

    private Language $language = Language::EN;

    #[SerializedName(serializedName: 'ttlSec')]
    private ?int $ttl = null;

    #[Ignore]
    public function getRequestMethod(): string
    {
        return 'POST';
    }

    #[Ignore]
    public function getEndpointPath(): string
    {
        return '/payment/init';
    }

    #[Ignore]
    public function getResponseClass(): string
    {
        return PaymentInitResponse::class;
    }

    /**
     * @return string
     */
    public function getOrderNumber(): string
    {
        return $this->orderNumber;
    }

    /**
     * @param string $orderNumber
     */
    public function setOrderNumber(string $orderNumber): void
    {
        $this->orderNumber = $orderNumber;
    }

    /**
     * @return PaymentOperation|null
     */
    public function getPaymentOperation(): ?PaymentOperation
    {
        return $this->paymentOperation;
    }

    /**
     * @param PaymentOperation|null $paymentOperation
     */
    public function setPaymentOperation(?PaymentOperation $paymentOperation): void
    {
        $this->paymentOperation = $paymentOperation;
    }

    /**
     * @return PaymentMethod|null
     */
    public function getPaymentMethod(): ?PaymentMethod
    {
        return $this->paymentMethod;
    }

    /**
     * @param PaymentMethod|null $paymentMethod
     */
    public function setPaymentMethod(?PaymentMethod $paymentMethod): void
    {
        $this->paymentMethod = $paymentMethod;
    }

    /**
     * @return float
     */
    public function getTotalAmount(): float
    {
        return $this->totalAmount / 100;
    }

    public function getRawTotalAmount(): int
    {
        return $this->totalAmount;
    }

    /**
     * @param float $totalAmount
     */
    public function setTotalAmount(float $totalAmount): void
    {
        $this->totalAmount = (int) round(($totalAmount * 100));
    }

    /**
     * @return Currency
     */
    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    /**
     * @param Currency $currency
     */
    public function setCurrency(Currency $currency): void
    {
        $this->currency = $currency;
    }

    /**
     * @return string
     */
    public function getReturnUrl(): string
    {
        return $this->returnUrl;
    }

    /**
     * @param string $returnUrl
     */
    public function setReturnUrl(string $returnUrl): void
    {
        $this->returnUrl = $returnUrl;
    }

    /**
     * @return HttpMethod
     */
    public function getReturnMethod(): HttpMethod
    {
        return $this->returnMethod;
    }

    /**
     * @param HttpMethod $returnMethod
     */
    public function setReturnMethod(HttpMethod $returnMethod): void
    {
        $this->returnMethod = $returnMethod;
    }

    /**
     * @return bool|null
     */
    public function getClosePayment(): ?bool
    {
        return $this->closePayment;
    }

    /**
     * @param bool|null $closePayment
     */
    public function setClosePayment(?bool $closePayment): void
    {
        $this->closePayment = $closePayment;
    }

    public function addCartItem(CartItem $cartItem): void
    {
        $this->cartItems[] = $cartItem;
    }

    /**
     * @return array<int, CartItem>
     */
    public function getCartItems(): array
    {
        return $this->cartItems;
    }

    /**
     * @return Customer|null
     */
    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    /**
     * @param Customer|null $customer
     */
    public function setCustomer(?Customer $customer): void
    {
        $this->customer = $customer;
    }

    /**
     * @return Order|null
     */
    public function getOrder(): ?Order
    {
        return $this->order;
    }

    /**
     * @param Order|null $order
     */
    public function setOrder(?Order $order): void
    {
        $this->order = $order;
    }

    /**
     * @return Language
     */
    public function getLanguage(): Language
    {
        return $this->language;
    }

    /**
     * @param Language $language
     */
    public function setLanguage(Language $language): void
    {
        $this->language = $language;
    }

    /**
     * @return int|null
     */
    public function getTtl(): ?int
    {
        return $this->ttl;
    }

    /**
     * @param int|null $ttl
     */
    public function setTtl(?int $ttl): void
    {
        $this->ttl = $ttl;
    }

    /**
     * @return string|null
     */
    public function getMerchantData(): ?string
    {
        return $this->merchantData;
    }

    /**
     * @param string|null $merchantData
     */
    public function setMerchantData(?string $merchantData): void
    {
        $this->merchantData = $merchantData;
    }
}
