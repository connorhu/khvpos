<?php declare(strict_types=1);

namespace KHTools\VPos\Requests;

use KHTools\VPos\Models\Merchant;
use KHTools\VPos\Normalizers\NormalizerResultOrderingHelper;
use KHTools\VPos\Requests\Traits\MerchantTrait;
use KHTools\VPos\Responses\GooglePayEchoResponse;
use Symfony\Component\Serializer\Annotation\Ignore;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;

/** @implements RequestInterface<GooglePayEchoResponse> */
class GooglePayEchoRequest implements RequestInterface
{
	use MerchantTrait;

	#[Ignore]
	public function getRequestMethod(): string
	{
		return 'POST';
	}

	#[Ignore]
	public function getEndpointPath(): string
	{
		return '/googlepay/echo';
	}

	#[Ignore]
	public function getResponseClass(): string
	{
		return GooglePayEchoResponse::class;
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
				'dttm',
			],
		];
	}
}
