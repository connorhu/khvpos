<?php declare(strict_types=1);

namespace KHTools\VPos\Models;

use Symfony\Component\Serializer\Annotation\SerializedName;

class InitParams
{
	#[SerializedName(serializedName: 'merchantIdentifier')]
	private ?string $merchantIdentifier = null;

	#[SerializedName(serializedName: 'merchantName')]
	private ?string $merchantName = null;

	#[SerializedName(serializedName: 'merchantCountry')]
	private ?string $merchantCountry = null;

	/**
	 * @var list<string>|null
	 */
	#[SerializedName(serializedName: 'supportedNetworks')]
	private ?array $supportedNetworks = null;

	public function getMerchantIdentifier(): ?string { return $this->merchantIdentifier; }

	public function setMerchantIdentifier(?string $merchantIdentifier): self
	{
		$this->merchantIdentifier = $merchantIdentifier;

		return $this;
	}

	public function getMerchantName(): ?string { return $this->merchantName; }

	public function setMerchantName(?string $merchantName): self
	{
		$this->merchantName = $merchantName;

		return $this;
	}

	public function getMerchantCountry(): ?string { return $this->merchantCountry; }

	public function setMerchantCountry(?string $merchantCountry): self
	{
		$this->merchantCountry = $merchantCountry;

		return $this;
	}

	/**
	 * @return list<string>|null
	 */
	public function getSupportedNetworks(): ?array { return $this->supportedNetworks; }

	/**
	 * @param list<string>|null $supportedNetworks
	 */
	public function setSupportedNetworks(?array $supportedNetworks): self
	{
		$this->supportedNetworks = $supportedNetworks;

		return $this;
	}
}
