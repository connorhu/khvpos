<?php

namespace KHTools\VPos\Responses;

interface ResponseInterface
{
    public function getResultCode(): ?int;

    public function getResultMessage(): ?string;

    /**
     * Returns the field names in the order they must appear when building the signature string.
     * This order is defined by the K&H API documentation for each response type.
     *
     * @return list<string>
     */
    public static function getSignatureFieldOrder(): array;
}
