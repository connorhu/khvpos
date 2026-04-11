<?php declare(strict_types=1);

namespace KHTools\VPos\Models;

class Fingerprint
{
	/**
	 * @var array<string, mixed>|null
	 */
	private ?array $browserData = null;

	/**
	 * @var array<string, mixed>|null
	 */
	private ?array $sdkData = null;

	/**
	 * @return array<string, mixed>|null
	 */
	public function getBrowserData(): ?array
	{
		return $this->browserData;
	}

	/**
	 * @param array<string, mixed>|null $browserData
	 */
	public function setBrowserData(?array $browserData): self
	{
		$this->browserData = $browserData;

		return $this;
	}

	/**
	 * @return array<string, mixed>|null
	 */
	public function getSdkData(): ?array
	{
		return $this->sdkData;
	}

	/**
	 * @param array<string, mixed>|null $sdkData
	 */
	public function setSdkData(?array $sdkData): self
	{
		$this->sdkData = $sdkData;

		return $this;
	}
}
