<?php

namespace KHTools\VPos\Responses\Traits;

trait CommonResponseTrait
{
    private ?int $resultCode = null;

    private ?string $resultMessage = null;

    /**
     * @return int
     */
    public function getResultCode(): int
    {
        return $this->resultCode;
    }

    /**
     * @param int $resultCode
     */
    public function setResultCode(int $resultCode): void
    {
        $this->resultCode = $resultCode;
    }

    /**
     * @return string
     */
    public function getResultMessage(): string
    {
        return $this->resultMessage;
    }

    /**
     * @param string $resultMessage
     */
    public function setResultMessage(string $resultMessage): void
    {
        $this->resultMessage = $resultMessage;
    }
}