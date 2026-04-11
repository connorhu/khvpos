<?php declare(strict_types=1);

namespace KHTools\VPos\Requests;

use KHTools\VPos\Models\Fingerprint;
use KHTools\VPos\Models\Merchant;
use KHTools\VPos\Normalizers\NormalizerResultOrderingHelper;
use KHTools\VPos\Requests\Traits\MerchantTrait;
use KHTools\VPos\Requests\Traits\PaymentIdTrait;
use KHTools\VPos\Responses\OneClickProcessResponse;
use Symfony\Component\Serializer\Annotation\Ignore;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;

/** @implements RequestInterface<OneClickProcessResponse> */
class OneClickProcessRequest implements RequestInterface
{
	use MerchantTrait;
	use PaymentIdTrait;

	private ?Fingerprint $fingerprint = null;

	#[Ignore]
	public function getRequestMethod(): string
	{
		return 'POST';
	}

	#[Ignore]
	public function getEndpointPath(): string
	{
		return '/oneclick/process';
	}

	#[Ignore]
	public function getResponseClass(): string
	{
		return OneClickProcessResponse::class;
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
				'merchantId', 'payId', 'dttm', 'fingerprint',
			],
		];
	}

	public function getFingerprint(): ?Fingerprint { return $this->fingerprint; }
	public function setFingerprint(?Fingerprint $fingerprint): void { $this->fingerprint = $fingerprint; }
}
