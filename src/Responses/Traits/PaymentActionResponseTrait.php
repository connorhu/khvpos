<?php declare(strict_types=1);

namespace KHTools\VPos\Responses\Traits;

use Symfony\Component\Serializer\Annotation\SerializedName;

trait PaymentActionResponseTrait
{
	#[SerializedName(serializedName: 'payId')]
	private ?string $paymentId = null;

	private ?int $paymentStatus = null;

	private ?string $statusDetail = null;

	/**
	 * @var array<string, mixed>|null
	 */
	#[SerializedName(serializedName: 'actions')]
	private ?array $authenticateAction = null;

	public function getPaymentId(): ?string
	{
		return $this->paymentId;
	}

	public function setPaymentId(?string $paymentId): void
	{
		$this->paymentId = $paymentId;
	}

	public function getPaymentStatus(): ?int
	{
		return $this->paymentStatus;
	}

	public function setPaymentStatus(?int $paymentStatus): void
	{
		$this->paymentStatus = $paymentStatus;
	}

	public function getStatusDetail(): ?string
	{
		return $this->statusDetail;
	}

	public function setStatusDetail(?string $statusDetail): void
	{
		$this->statusDetail = $statusDetail;
	}

	/**
	 * @return array<string, mixed>|null
	 */
	public function getAuthenticateAction(): ?array
	{
		return $this->authenticateAction;
	}

	/**
	 * @param array<string, mixed>|null $authenticateAction
	 */
	public function setAuthenticateAction(?array $authenticateAction): void
	{
		$this->authenticateAction = $authenticateAction;
	}
}
