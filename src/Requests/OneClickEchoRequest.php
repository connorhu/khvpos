<?php declare(strict_types=1);

namespace KHTools\VPos\Requests;

use KHTools\VPos\Models\Merchant;
use KHTools\VPos\Normalizers\NormalizerResultOrderingHelper;
use KHTools\VPos\Requests\Traits\MerchantTrait;
use KHTools\VPos\Responses\OneClickEchoResponse;
use Symfony\Component\Serializer\Annotation\Ignore;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;

/** @implements RequestInterface<OneClickEchoResponse> */
class OneClickEchoRequest implements RequestInterface
{
	use MerchantTrait;

	#[SerializedName(serializedName: 'origPayId')]
	private ?string $originalPaymentId = null;

	#[Ignore]
	public function getRequestMethod(): string
	{
		return 'POST';
	}

	#[Ignore]
	public function getEndpointPath(): string
	{
		return '/oneclick/echo';
	}

	#[Ignore]
	public function getResponseClass(): string
	{
		return OneClickEchoResponse::class;
	}

	#[Ignore]
	public function getNormalizationContext(): array
	{
		return [
			AbstractNormalizer::CALLBACKS => [
				'merchant' => function (Merchant $value): string {
					return $value->merchantId;
				},
			],
			AbstractObjectNormalizer::SKIP_NULL_VALUES => true,
			NormalizerResultOrderingHelper::ORDER => [
				'merchantId',
				'origPayId',
				'dttm',
			],
		];
	}

	public function getOriginalPaymentId(): ?string
	{
		return $this->originalPaymentId;
	}

	public function setOriginalPaymentId(?string $originalPaymentId): void
	{
		$this->originalPaymentId = $originalPaymentId;
	}
}
