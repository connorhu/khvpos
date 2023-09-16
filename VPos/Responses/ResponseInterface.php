<?php

namespace KHTools\VPos\Responses;

interface ResponseInterface
{
    public function getResultCode(): int;

    public function getResultMessage(): string;


}