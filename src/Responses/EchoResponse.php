<?php

namespace KHTools\VPos\Responses;

use KHTools\VPos\Responses\Traits\CommonResponseTrait;

class EchoResponse implements ResponseInterface
{
    use CommonResponseTrait;

    public static function getSignatureFieldOrder(): array
    {
        return ['dttm', 'resultCode', 'resultMessage'];
    }
}