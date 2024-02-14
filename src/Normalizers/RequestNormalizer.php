<?php

namespace KHTools\VPos\Normalizers;

use KHTools\VPos\Entities\Merchant;
use KHTools\VPos\Requests\EchoRequest;
use KHTools\VPos\Requests\PaymentCloseRequest;
use KHTools\VPos\Requests\PaymentInitRequest;
use KHTools\VPos\Requests\PaymentProcessRequest;
use KHTools\VPos\Requests\PaymentRefundRequest;
use KHTools\VPos\Requests\PaymentReverseRequest;
use KHTools\VPos\Requests\PaymentStatusRequest;
use KHTools\VPos\Requests\RequestInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class RequestNormalizer implements NormalizerInterface
{
    public function __construct(
        private readonly ObjectNormalizer $objectNormalizer,
    )
    {
    }

    protected function getContextWithObject(RequestInterface $object): array
    {
        if ($object instanceof EchoRequest) {
            return [
                AbstractNormalizer::CALLBACKS => [
                    'merchant' => function (Merchant $value) {
                        return $value->merchantId;
                    }
                ]
            ];
        } elseif ($object instanceof PaymentInitRequest) {
            return [
                AbstractNormalizer::CALLBACKS => [
                    'totalAmount' => function (float $value, PaymentInitRequest $paymentInit) {
                        return $paymentInit->getRawTotalAmount();
                    },
                    'merchant' => function (Merchant $value) {
                        return $value->merchantId;
                    }
                ],
                AbstractNormalizer::IGNORED_ATTRIBUTES => [
                    'rawTotalAmount',
                ],
                AbstractObjectNormalizer::SKIP_NULL_VALUES => true,
                DateTimeNormalizer::FORMAT_KEY => 'c',
                NormalizerResultOrderingHelper::ORDER => [
                    'merchantId',
                    'orderNo',
                    'dttm',
                    'payOperation',
                    'payMethod',
                    'totalAmount',
                    'currency',
                    'closePayment',
                    'returnUrl',
                    'returnMethod',
                    'cart',
                    'customer',
                    'order',
                    'merchantData',
                    'language',
                    'ttlSec',
                ],
            ];
        } elseif ($object instanceof PaymentStatusRequest || $object instanceof PaymentProcessRequest || $object instanceof PaymentReverseRequest) {
            return [
                AbstractNormalizer::CALLBACKS => [
                    'merchant' => function (Merchant $value) {
                        return $value->merchantId;
                    }
                ],
                AbstractObjectNormalizer::SKIP_NULL_VALUES => true,
                NormalizerResultOrderingHelper::ORDER => [
                    'merchantId',
                    'payId',
                    'dttm',
                ],
            ];
        } elseif ($object instanceof PaymentCloseRequest) {
            return [
                AbstractNormalizer::IGNORED_ATTRIBUTES => [
                    'rawTotalAmount'
                ],
                AbstractNormalizer::CALLBACKS => [
                    'totalAmount' => function (float $value, PaymentCloseRequest $request) {
                        return $request->getRawTotalAmount();
                    },
                ],
                AbstractObjectNormalizer::SKIP_NULL_VALUES => true,
                NormalizerResultOrderingHelper::ORDER => [
                    'merchantId',
                    'payId',
                    'dttm',
                    'totalAmount',
                ],
            ];
        } elseif ($object instanceof PaymentRefundRequest) {
            return [
                AbstractNormalizer::CALLBACKS => [
                    'merchant' => function (Merchant $value) {
                        return $value->merchantId;
                    },
                    'amount' => function (float $value, PaymentRefundRequest $request) {
                        return $request->getRawAmount();
                    },
                ],
                AbstractNormalizer::IGNORED_ATTRIBUTES => [
                    'rawAmount',
                ],
                AbstractObjectNormalizer::SKIP_NULL_VALUES => true,
                NormalizerResultOrderingHelper::ORDER => [
                    'merchantId',
                    'payId',
                    'dttm',
                    'amount',
                ],
            ];
        }

        throw new \UnhandledMatchError('Unknown request type: '. $object::class);
    }

    public function normalize(mixed $object, string $format = null, array $context = []): array
    {
        $context = $this->getContextWithObject($object);
        $normalized = $this->objectNormalizer->normalize($object, $format, $context);
        $normalized['dttm'] = date("YmdHis");

        if (isset($context[NormalizerResultOrderingHelper::ORDER])) {
            $normalized = NormalizerResultOrderingHelper::orderArray($normalized, $context[NormalizerResultOrderingHelper::ORDER]);
        }

        if (isset($normalized['order']['giftcards']) && count($normalized['order']['giftcards']) === 0) {
            unset($normalized['order']['giftcards']);
        }

        return $normalized;
    }

    public function supportsNormalization(mixed $data, string $format = null, array $context = []): bool
    {
        return $data instanceof RequestInterface;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            '*' => null,
            RequestInterface::class => true,
        ];
    }
}
