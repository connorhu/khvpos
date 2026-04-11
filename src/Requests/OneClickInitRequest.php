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
use KHTools\VPos\Responses\OneClickInitResponse;
use Symfony\Component\Serializer\Annotation\Ignore;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

class OneClickInitRequest implements RequestInterface
{
	use MerchantTrait;

	#[SerializedName(serializedName: 'origPayId')]
	private string $originalPaymentId;

	#[SerializedName(serializedName: 'orderNo')]
	private string $orderNumber;

	private string $returnUrl;

	private HttpMethod $returnMethod;

	private ?string $clientIp = null;

	private ?int $totalAmount = null;

	private ?Currency $currency = null;

	private ?bool $closePayment = null;

	private ?Customer $customer = null;

	private ?Order $order = null;

	private ?bool $clientInitiated = null;

	private ?bool $sdkUsed = null;

	private ?string $merchantData = null;

	private ?Language $language = null;

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
		return '/oneclick/init';
	}

	#[Ignore]
	public function getResponseClass(): string
	{
		return OneClickInitResponse::class;
	}

	#[Ignore]
	public function getNormalizationContext(): array
	{
		return [
			AbstractNormalizer::CALLBACKS => [
				'merchant' => function (Merchant $value): string {
					return $value->merchantId;
				},
				'totalAmount' => function (?int $value): ?int {
					return $value;
				},
			],
			AbstractObjectNormalizer::SKIP_NULL_VALUES => true,
			DateTimeNormalizer::FORMAT_KEY => 'c',
			NormalizerResultOrderingHelper::ORDER => [
				'merchantId', 'origPayId', 'orderNo', 'dttm',
				'returnUrl', 'returnMethod',
				'clientIp', 'totalAmount', 'currency', 'closePayment',
				'customer', 'order', 'clientInitiated', 'sdkUsed',
				'merchantData', 'language', 'ttlSec',
			],
		];
	}

	public function getOriginalPaymentId(): string { return $this->originalPaymentId; }
	public function setOriginalPaymentId(string $originalPaymentId): void { $this->originalPaymentId = $originalPaymentId; }

	public function getOrderNumber(): string { return $this->orderNumber; }
	public function setOrderNumber(string $orderNumber): void { $this->orderNumber = $orderNumber; }

	public function getReturnUrl(): string { return $this->returnUrl; }
	public function setReturnUrl(string $returnUrl): void { $this->returnUrl = $returnUrl; }

	public function getReturnMethod(): HttpMethod { return $this->returnMethod; }
	public function setReturnMethod(HttpMethod $returnMethod): void { $this->returnMethod = $returnMethod; }

	public function getClientIp(): ?string { return $this->clientIp; }
	public function setClientIp(?string $clientIp): void { $this->clientIp = $clientIp; }

	public function getTotalAmount(): ?int { return $this->totalAmount; }
	public function setTotalAmount(?int $totalAmount): void { $this->totalAmount = $totalAmount; }

	public function getCurrency(): ?Currency { return $this->currency; }
	public function setCurrency(?Currency $currency): void { $this->currency = $currency; }

	public function getClosePayment(): ?bool { return $this->closePayment; }
	public function setClosePayment(?bool $closePayment): void { $this->closePayment = $closePayment; }

	public function getCustomer(): ?Customer { return $this->customer; }
	public function setCustomer(?Customer $customer): void { $this->customer = $customer; }

	public function getOrder(): ?Order { return $this->order; }
	public function setOrder(?Order $order): void { $this->order = $order; }

	public function getClientInitiated(): ?bool { return $this->clientInitiated; }
	public function setClientInitiated(?bool $clientInitiated): void { $this->clientInitiated = $clientInitiated; }

	public function getSdkUsed(): ?bool { return $this->sdkUsed; }
	public function setSdkUsed(?bool $sdkUsed): void { $this->sdkUsed = $sdkUsed; }

	public function getMerchantData(): ?string { return $this->merchantData; }
	public function setMerchantData(?string $merchantData): void { $this->merchantData = $merchantData; }

	public function getLanguage(): ?Language { return $this->language; }
	public function setLanguage(?Language $language): void { $this->language = $language; }

	public function getTtl(): ?int { return $this->ttl; }
	public function setTtl(?int $ttl): void { $this->ttl = $ttl; }
}
