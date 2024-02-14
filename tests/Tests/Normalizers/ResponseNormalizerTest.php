<?php

namespace KHTools\Tests\Normalizers;

use KHTools\VPos\Entities\Enums\HttpMethod;
use KHTools\VPos\Normalizers\EnumNormalizer;
use KHTools\VPos\Normalizers\ResponseNormalizer;
use KHTools\VPos\Responses\PaymentStatusResponse;
use KHTools\VPos\SignatureProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AttributeLoader;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\NameConverter\MetadataAwareNameConverter;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class ResponseNormalizerTest extends TestCase
{
    private NormalizerInterface $normalizer;

    protected function setUp(): void
    {
        $loader = class_exists(AttributeLoader::class) ? new AttributeLoader() : new AnnotationLoader();
        $classMetadataFactory = new ClassMetadataFactory($loader);
        $metadataAwareNameConverter = new MetadataAwareNameConverter($classMetadataFactory);
        $extractor = new PropertyInfoExtractor([], [new ReflectionExtractor()]);

        $enumNormalizer = new EnumNormalizer();
        $objectNormalizer = new ObjectNormalizer($classMetadataFactory, $metadataAwareNameConverter, null, $extractor);
        $signatureProvider = new SignatureProvider([], './');

        $this->normalizer = new Serializer([
            new ResponseNormalizer($signatureProvider, $objectNormalizer),
            $enumNormalizer,
            $objectNormalizer,
        ]);

        $objectNormalizer->setSerializer($this->normalizer);
    }

    public function testPaymentStatusResponse()
    {
        $response = [
            'payId' => 'ff41e84b7e33@HA',
            'dttm' => '20220125131601',
            'resultCode' => 0,
            'resultMessage' => 'OK',
            'paymentStatus' => 4,
            'actions' => [
                'authenticate' => [
                    'browserChallenge' => [
                        'url' => 'https://example.com/challenge-endpoint',
                        'method' => 'POST',
                        'vars' => [
                            'key1' => 'value1',
                        ],
                    ],
                    'sdkChallenge' => [
                        'threeDSServerTransID' => 'eeddda80-6ca7-4b22-9d6a-eb8e84791ec9',
                        'acsReferenceNumber' => '3DS_LOA_ACS_201_13579',
                        'acsTransID' => '7f3296a8-08c4-4afb-a3e2-8ce31b2e9069',
                        'acsSignedContent' => 'base64-encoded-acs-signed-content'
                    ],
                ],
                'fingerprint' => [
                    'browserInit' => [
                        'url' => 'https://example.com/init-endpoint',
                        'method' => 'POST',
                        'vars' => [
                            'key1' => 'value1',
                        ],
                    ],
                    'sdkInit' => [
                        'directoryServerID' => 'A000000003',
                        'schemeId' => 'Visa',
                        'messageVersion' => '2.2.0',
                    ],
                ],
            ],
            'authCode' => 'F7A23E',
        ];

        $response = $this->normalizer->denormalize($response, PaymentStatusResponse::class, 'array');

        $this->assertSame('ff41e84b7e33@HA', $response->getPaymentId());
        $this->assertSame(0, $response->getResultCode());
        $this->assertSame('OK', $response->getResultMessage());
        $this->assertSame(4, $response->getPaymentStatus());
        $this->assertSame('F7A23E', $response->getAuthorizationCode());

        $this->assertSame('https://example.com/challenge-endpoint', $response->getAuthenticateAction()->browserChallenge->url);
        $this->assertSame(HttpMethod::Post, $response->getAuthenticateAction()->browserChallenge->method);
        $this->assertSame([
            'key1' => 'value1'
        ], $response->getAuthenticateAction()->browserChallenge->vars);

        $this->assertSame('eeddda80-6ca7-4b22-9d6a-eb8e84791ec9', $response->getAuthenticateAction()->sdkChallenge->threeDSServerTransID);
        $this->assertSame('3DS_LOA_ACS_201_13579', $response->getAuthenticateAction()->sdkChallenge->acsReferenceNumber);
        $this->assertSame('7f3296a8-08c4-4afb-a3e2-8ce31b2e9069', $response->getAuthenticateAction()->sdkChallenge->acsTransID);
        $this->assertSame('base64-encoded-acs-signed-content', $response->getAuthenticateAction()->sdkChallenge->acsSignedContent);
    }

}
