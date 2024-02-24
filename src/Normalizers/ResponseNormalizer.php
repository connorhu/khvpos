<?php

namespace KHTools\VPos\Normalizers;

use KHTools\VPos\Models\Authenticate;
use KHTools\VPos\Exceptions\VerificationFailedException;
use KHTools\VPos\Responses\EchoResponse;
use KHTools\VPos\Responses\PaymentCloseResponse;
use KHTools\VPos\Responses\PaymentInitResponse;
use KHTools\VPos\Responses\PaymentProcessResponse;
use KHTools\VPos\Responses\PaymentRefundResponse;
use KHTools\VPos\Responses\PaymentReverseResponse;
use KHTools\VPos\Responses\PaymentStatusResponse;
use KHTools\VPos\Responses\ResponseInterface;
use KHTools\VPos\SignatureProviderInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class ResponseNormalizer implements DenormalizerInterface
{
    public function __construct(
        private readonly SignatureProviderInterface $signatureProvider,
        private readonly ObjectNormalizer $objectNormalizer,
    )
    {
    }

    private function responseKeyOrderWithClass(string $class): array
    {
        return match ($class) {
            EchoResponse::class => ['dttm', 'resultCode', 'resultMessage'],
            PaymentInitResponse::class, PaymentReverseResponse::class => [
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
            PaymentProcessResponse::class => [
                'payId',
                'dttm',
                'resultCode',
                'resultMessage',
                'paymentStatus',
                'authCode',
                'merchantData',
                'statusDetail',
            ],
            PaymentCloseResponse::class, PaymentRefundResponse::class => [
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

    public function denormalize(mixed $data, string $type, string $format = null, array $context = []): object
    {
        $signature = $data['signature'] ?? null;
        $data = NormalizerResultOrderingHelper::orderArray($data, self::responseKeyOrderWithClass($type));

        if ($signature !== null) {
            $verificationResult = $this->signatureProvider->verify($data, $signature);
            if ($verificationResult === false) {
                throw new VerificationFailedException();
            }
        }

        $object = $this->objectNormalizer->denormalize($data, $type, $format);

        if ($object instanceof PaymentStatusResponse && isset($data['actions'])) {
            if (isset($data['actions']['authenticate'])) {
                $authenticate = $this->objectNormalizer->denormalize($data['actions']['authenticate'], Authenticate::class, 'array');
                $object->setAuthenticateAction($authenticate);
            }

            /*if (isset($data['actions']['fingerprint'])) {
                $authenticate = $this->objectNormalizer->denormalize($data['actions']['fingerprint'], Authenticate::class, 'array');
                $object->setAuthenticateAction($authenticate);
            }*/
        }

        return $object;
    }

    public function supportsDenormalization(mixed $data, string $type, string $format = null, array $context = []): bool
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
