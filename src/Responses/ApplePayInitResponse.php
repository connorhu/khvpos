<?php declare(strict_types=1);

namespace KHTools\VPos\Responses;

use KHTools\VPos\Responses\Traits\CommonResponseTrait;
use KHTools\VPos\Responses\Traits\PaymentActionResponseTrait;

class ApplePayInitResponse implements ResponseInterface
{
	use CommonResponseTrait;
	use PaymentActionResponseTrait;

	public static function getSignatureFieldOrder(): array
	{
		return ['payId', 'dttm', 'resultCode', 'resultMessage', 'paymentStatus', 'statusDetail', 'actions'];
	}
}
