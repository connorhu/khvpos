<?php declare(strict_types=1);

namespace KHTools\VPos;

use KHTools\VPos\Models\Merchant;

interface SignatureProviderInterface
{
    /**
     * @param array<string, mixed> $contentToSign
     */
    public function sign(Merchant $merchant, array $contentToSign): string;

    /**
     * @param array<string, mixed> $signedContent
     */
    public function verify(array $signedContent, string $signature): bool;
}
