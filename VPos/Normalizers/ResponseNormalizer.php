<?php

namespace KHTools\VPos\Normalizers;

use KHTools\VPos\Exceptions\VerificationFailedException;
use KHTools\VPos\Responses\EchoResponse;
use KHTools\VPos\Responses\PaymentInitResponse;
use KHTools\VPos\Responses\PaymentStatusResponse;
use KHTools\VPos\Responses\ResponseInterface;
use KHTools\VPos\SignatureProvider;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class ResponseNormalizer implements DenormalizerInterface
{
    public function __construct(
        private readonly SignatureProvider $signatureProvider,
        private readonly ObjectNormalizer $objectNormalizer,
    )
    {
    }

    private function responseKeyOrderWithClass(string $class): array
    {
        return match ($class) {
            EchoResponse::class => ['merchantId', 'dttm'],
            PaymentInitResponse::class => [
                'payId',
                'dttm',
                'resultCode',
                'resultMessage',
                'paymentStatus',
                'statusDetail',
            ],
            PaymentStatusResponse::class => [
                'payId',
                'dttm',
                'resultCode',
                'resultMessage',
                'paymentStatus',
                'authCode',
                'statusDetail',
                'actions',
            ],
            'TODD reverse class' => [
                'payId',
                'dttm',
                'resultCode',
                'resultMessage',
                'paymentStatus',
                'statusDetail',
            ],
            'TODD close class' => [
                'payId',
                'dttm',
                'resultCode',
                'resultMessage',
                'paymentStatus',
                'authCode',
                'statusDetail',
            ],
            default => throw new \UnhandledMatchError('unnkown class type: '. $class),
        };
    }

    public function denormalize(mixed $data, string $type, string $format = null, array $context = [])
    {
        $data = NormalizerResultOrderingHelper::orderArray($data, self::responseKeyOrderWithClass($type));

        if (isset($data['signature'])) {
            $verificationResult = $this->signatureProvider->verify($data, $data['signature']);
            if ($verificationResult === false) {
                throw new VerificationFailedException();
            }
        }

        return $this->objectNormalizer->denormalize($data, $type, $format);
    }

    public function supportsDenormalization(mixed $data, string $type, string $format = null): bool
    {
        return class_implements($type)[ResponseInterface::class] === ResponseInterface::class;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            '*' => null,
            ResponseInterface::class => true,
        ];
    }
}