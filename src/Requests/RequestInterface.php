<?php declare(strict_types=1);

namespace KHTools\VPos\Requests;

use KHTools\VPos\Models\Merchant;

interface RequestInterface
{
    public function getRequestMethod(): string;

    public function getEndpointPath(): string;

    public function getMerchant(): Merchant;

    public function setMerchant(Merchant $merchant): void;

    /**
     * @return class-string
     */
    public function getResponseClass(): string;

    /**
     * Returns the Symfony Serializer normalizer context to use when serializing this request.
     * Implementations must annotate this method with #[Ignore] to prevent it from appearing
     * in the serialized output.
     *
     * @return array<string, mixed>
     */
    public function getNormalizationContext(): array;
}
