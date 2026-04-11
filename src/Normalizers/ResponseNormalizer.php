<?php

namespace KHTools\VPos\Normalizers;

use KHTools\VPos\Models\Authenticate;
use KHTools\VPos\Exceptions\VerificationFailedException;
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
    ) {
    }

    public function denormalize(mixed $data, string $type, string $format = null, array $context = []): object
    {
        $signature = $data['signature'] ?? null;
        $data = NormalizerResultOrderingHelper::orderArray($data, $type::getSignatureFieldOrder());

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
        }

        return $object;
    }

    public function supportsDenormalization(mixed $data, string $type, string $format = null, array $context = []): bool
    {
        return is_a($type, ResponseInterface::class, true);
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            '*' => null,
            ResponseInterface::class => true,
        ];
    }
}
