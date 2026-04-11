<?php declare(strict_types=1);

namespace KHTools\VPos\Requests;

use KHTools\VPos\Models\Customer;
use KHTools\VPos\Models\Enums\Currency;
use KHTools\VPos\Models\Enums\HttpMethod;
use KHTools\VPos\Models\Enums\Language;
use KHTools\VPos\Models\Merchant;
use KHTools\VPos\Models\Order;
use KHTools\VPos\Normalizers\NormalizerResultOrderingHelper;
use KHTools\VPos\Requests\Traits\MerchantTrait;
use KHTools\VPos\Responses\GooglePayInitResponse;
use Symfony\Component\Serializer\Annotation\Ignore;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

class GooglePayInitRequest implements RequestInterface
{
	use MerchantTrait;

	#[SerializedName(serializedName: 'orderNo')]
	private string $orderNumber;

	private int $totalAmount;

	private Currency $currency;

	private string $payload;

	private string $returnUrl;

	private HttpMethod $returnMethod;

	private ?string $clientIp = null;

	private ?bool $closePayment = null;

	private ?Customer $customer = null;

	private ?Order $order = null;

	private ?bool $sdkUsed = null;

	private ?string $merchantData = null;

	private ?Language $language = null;

	#[SerializedName(serializedName: 'ttlSec')]
	private ?int $ttl = null;

	#[Ignore]
	public function getRequestMethod(): string { return 'POST'; }

	#[Ignore]
	public function getEndpointPath(): string { return '/googlepay/init'; }

	#[Ignore]
	public function getResponseClass(): string { return GooglePayInitResponse::class; }

	#[Ignore]
	public function getNormalizationContext(): array
	{
		return [
			AbstractNormalizer::CALLBACKS => [
				'merchant' => function (Merchant $value): string {
					return $value->merchantId;
				},
				'totalAmount' => function (int $value, GooglePayInitRequest $object): int {
					return $object->getRawTotalAmount();
				},
			],
			AbstractNormalizer::IGNORED_ATTRIBUTES => ['rawTotalAmount'],
			AbstractObjectNormalizer::SKIP_NULL_VALUES => true,
			DateTimeNormalizer::FORMAT_KEY => 'c',
			NormalizerResultOrderingHelper::ORDER => [
				'merchantId', 'orderNo', 'dttm', 'clientIp',
				'totalAmount', 'currency', 'closePayment', 'payload',
				'returnUrl', 'returnMethod', 'customer', 'order',
				'sdkUsed', 'merchantData', 'language', 'ttlSec',
			],
		];
	}

	public function getTotalAmount(): float { return $this->totalAmount / 100; }

	public function getRawTotalAmount(): int { return $this->totalAmount; }

	public function setTotalAmount(float $totalAmount): void
	{
		$this->totalAmount = (int) \bcmul(number_format($totalAmount, 2, '.', ''), '100');
	}

	public function getOrderNumber(): string { return $this->orderNumber; }
	public function setOrderNumber(string $orderNumber): void { $this->orderNumber = $orderNumber; }

	public function getCurrency(): Currency { return $this->currency; }
	public function setCurrency(Currency $currency): void { $this->currency = $currency; }

	public function getPayload(): string { return $this->payload; }
	public function setPayload(string $payload): void { $this->payload = $payload; }

	public function getReturnUrl(): string { return $this->returnUrl; }
	public function setReturnUrl(string $returnUrl): void { $this->returnUrl = $returnUrl; }

	public function getReturnMethod(): HttpMethod { return $this->returnMethod; }
	public function setReturnMethod(HttpMethod $returnMethod): void { $this->returnMethod = $returnMethod; }

	public function getClientIp(): ?string { return $this->clientIp; }
	public function setClientIp(?string $clientIp): void { $this->clientIp = $clientIp; }

	public function getClosePayment(): ?bool { return $this->closePayment; }
	public function setClosePayment(?bool $closePayment): void { $this->closePayment = $closePayment; }

	public function getCustomer(): ?Customer { return $this->customer; }
	public function setCustomer(?Customer $customer): void { $this->customer = $customer; }

	public function getOrder(): ?Order { return $this->order; }
	public function setOrder(?Order $order): void { $this->order = $order; }

	public function getSdkUsed(): ?bool { return $this->sdkUsed; }
	public function setSdkUsed(?bool $sdkUsed): void { $this->sdkUsed = $sdkUsed; }

	public function getMerchantData(): ?string { return $this->merchantData; }
	public function setMerchantData(?string $merchantData): void { $this->merchantData = $merchantData; }

	public function getLanguage(): ?Language { return $this->language; }
	public function setLanguage(?Language $language): void { $this->language = $language; }

	public function getTtl(): ?int { return $this->ttl; }
	public function setTtl(?int $ttl): void { $this->ttl = $ttl; }
}
