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
}
