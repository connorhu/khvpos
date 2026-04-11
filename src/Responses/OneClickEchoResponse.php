<?php declare(strict_types=1);

namespace KHTools\VPos\Responses;

use KHTools\VPos\Responses\Traits\CommonResponseTrait;
use Symfony\Component\Serializer\Annotation\SerializedName;

class OneClickEchoResponse implements ResponseInterface
{
	use CommonResponseTrait;

	#[SerializedName(serializedName: 'origPayId')]
	private string $originalPaymentId;

	public static function getSignatureFieldOrder(): array
	{
		return ['origPayId', 'dttm', 'resultCode', 'resultMessage'];
	}

	public function getOriginalPaymentId(): string
	{
		return $this->originalPaymentId;
	}

	public function setOriginalPaymentId(string $originalPaymentId): void
	{
		$this->originalPaymentId = $originalPaymentId;
	}
}
