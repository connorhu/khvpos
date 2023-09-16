<?php declare(strict_types=1);

namespace KHTools\Tests\VPos;

use KHTools\VPos\Entities\Merchant;
use KHTools\VPos\Exceptions\ClientErrorException;
use KHTools\VPos\Exceptions\ServerErrorException;
use KHTools\VPos\Exceptions\UnhandledErrorException;
use KHTools\VPos\Normalizers\HttpErrorNormalizer;
use KHTools\VPos\Normalizers\RequestNormalizer;
use KHTools\VPos\Requests\EchoRequest;
use KHTools\VPos\VPosClient;
use KHTools\VPos\PaymentRequestArguments;
use KHTools\VPos\SignatureProvider;
use KHTools\VPos\TransactionInterface;
use KHTools\Tests\VPos\Fixtures\TransactionImplementation;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Psr18Client;
use Symfony\Component\HttpClient\Response\JsonMockResponse;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\Serializer\Encoder\JsonDecode;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\NameConverter\MetadataAwareNameConverter;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

class VPosClientErrorTest extends TestCase
{
    private function initErrorResponseTestClient(int $httpCode, int $resultCode, string $resultMessage): VPosClient
    {
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader());
        $metadataAwareNameConverter = new MetadataAwareNameConverter($classMetadataFactory);

        $httpErrorNormalizer = new HttpErrorNormalizer();
        $objectNormalizer = new ObjectNormalizer($classMetadataFactory, $metadataAwareNameConverter);
        $callback = function ($method, $url, $options) use ($httpCode, $resultCode, $resultMessage) : JsonMockResponse {
            $body = [
                'resultCode' => $resultCode,
                'resultMessage' => $resultMessage,
            ];
            $info = [
                'http_code' => $httpCode,
            ];
            return new JsonMockResponse($body, $info);
        };
        $psrClient = new Psr18Client(new MockHttpClient($callback));

        $serializer = new Serializer([
            new RequestNormalizer($objectNormalizer),
            $httpErrorNormalizer,
            $objectNormalizer,
        ], [
            new JsonDecode(),
        ]);

        $rsaPrivateKeyPath = __DIR__ .'/Fixtures/rsa_private_key.pem';
        $rsaPublicKeyPath = __DIR__ .'/Fixtures/rsa_public_key.pem';

        $container = new Container();
        $container->set(ClientInterface::class, $psrClient);
        $container->set(RequestFactoryInterface::class, $psrClient);
        $container->set(StreamFactoryInterface::class, $psrClient);
        $container->set(SignatureProvider::class, new SignatureProvider($rsaPrivateKeyPath, $rsaPublicKeyPath));
        $container->set(NormalizerInterface::class, $serializer);
        $container->set(SerializerInterface::class, $serializer);

        $vPosClient = new VPosClient(VPosClient::VERSION_REST_V1, '100', true);
        $vPosClient->setContainer($container);

        return $vPosClient;
    }

    /**
     * @dataProvider errorResponseDataProvider
     */
    public function testErrorResponse(int $httpCode, int $resultCode, string $resultMessage, string $expectedClass, int $expectedCode, string $expectedMessage): void
    {
        $vPosClient = $this->initErrorResponseTestClient($httpCode, $resultCode, $resultMessage);
        $request = new EchoRequest();
        $merchant = new Merchant();
        $merchant->merchantId = '100';
        $request->setMerchant($merchant);
        $this->expectException($expectedClass);
        $this->expectExceptionCode($expectedCode);
        $this->expectExceptionMessage($expectedMessage);
        $vPosClient->send($request);
    }

    public function errorResponseDataProvider(): \Generator
    {
        yield [400, 100, 'client error', ClientErrorException::class, 100, 'client error'];
        yield [401, 100, 'client error', ClientErrorException::class, 100, 'client error'];
        yield [403, 100, 'client error', ClientErrorException::class, 100, 'client error'];
        yield [404, 100, 'client error', ClientErrorException::class, 100, 'client error'];
        yield [405, 100, 'client error', ClientErrorException::class, 100, 'client error'];
        yield [429, 100, 'client error', ClientErrorException::class, 100, 'client error'];
        yield [500, 100, 'server error', ServerErrorException::class, 100, 'server error'];
        yield [503, 100, 'server error', ServerErrorException::class, 100, 'server error'];

        yield [401, 100, 'client error', ClientErrorException::class, 100, 'client error'];
        yield [401, 110, 'client error', ClientErrorException::class, 110, 'client error'];
        yield [401, 120, 'client error', ClientErrorException::class, 120, 'client error'];
        yield [401, 900, 'client error', ClientErrorException::class, 900, 'client error'];

        yield [999, 900, 'client error', UnhandledErrorException::class, 0, 'Unknown http status code: 999'];
    }
}