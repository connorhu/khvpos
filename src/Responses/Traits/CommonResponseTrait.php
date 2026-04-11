<?php

namespace KHTools\VPos\Responses\Traits;

trait CommonResponseTrait
{
    private ?int $resultCode = null;

    private ?string $resultMessage = null;

    public function getResultCode(): ?int
    {
        return $this->resultCode;
    }

    public function setResultCode(int $resultCode): void
    {
        $this->resultCode = $resultCode;
    }

    public function getResultMessage(): ?string
    {
        return $this->resultMessage;
    }

    public function setResultMessage(string $resultMessage): void
    {
        $this->resultMessage = $resultMessage;
    }
}