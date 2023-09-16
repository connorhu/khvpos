<?php declare(strict_types=1);

namespace KHTools\VPos;

use KHTools\VPos\Entities\Merchant;

interface SignatureProviderInterface
{
    public function sign(Merchant $merchant, array $contentToSign): string;

    public function verify(array $signedContent, string $signature): bool;
}