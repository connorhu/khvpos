<?php declare(strict_types=1);

namespace KHTools\VPos\Responses;

use KHTools\VPos\Models\InitParams;
use KHTools\VPos\Responses\Traits\CommonResponseTrait;

class GooglePayEchoResponse implements ResponseInterface
{
	use CommonResponseTrait;

	private ?InitParams $initParams = null;

	public static function getSignatureFieldOrder(): array
	{
		return ['dttm', 'resultCode', 'resultMessage', 'initParams'];
	}

	public function getInitParams(): ?InitParams { return $this->initParams; }
	public function setInitParams(?InitParams $initParams): void { $this->initParams = $initParams; }
}
