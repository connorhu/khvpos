<?php

namespace KHTools\VPos\Normalizers;

use KHTools\VPos\Exceptions\ClientErrorException;
use KHTools\VPos\Exceptions\HttpErrorException;
use KHTools\VPos\Exceptions\ServerErrorException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class HttpErrorNormalizer implements DenormalizerInterface
{
    public function denormalize(mixed $data, string $type, string $format = null, array $context = [])
    {
        return new $type($data['resultMessage'] ?? 'missing', $data['resultCode'] ?? 0);
    }

    public function supportsDenormalization(mixed $data, string $type, string $format = null): bool
    {
        return $type === ClientErrorException::class || $type === ServerErrorException::class;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            '*' => null,
            ClientErrorException::class => true,
            ServerErrorException::class => true,
            HttpErrorException::class => true,
        ];
    }
}