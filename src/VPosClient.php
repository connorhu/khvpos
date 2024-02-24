<?php declare(strict_types=1);

namespace KHTools\VPos;

use KHTools\VPos\Exceptions\ClientErrorException;
use KHTools\VPos\Exceptions\HttpErrorException;
use KHTools\VPos\Exceptions\InvalidArgumentException;
use KHTools\VPos\Exceptions\UnhandledErrorException;
use KHTools\VPos\Requests\PaymentProcessRequest;
use KHTools\VPos\Requests\RequestInterface;
use KHTools\VPos\Responses\ResponseInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Symfony\Component\Serializer\Encoder\JsonDecode;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Service\Attribute\SubscribedService;
use Symfony\Contracts\Service\ServiceSubscriberInterface;
use Symfony\Contracts\Service\ServiceSubscriberTrait;

class VPosClient implements ServiceSubscriberInterface
{
    use ServiceSubscriberTrait;

    public const VERSION_REST_V1 = 'rv1';

    public const VERSIONS = [
        self::VERSION_REST_V1,
    ];

    private ClientInterface $httpClient;

    public function __construct(
        private readonly string $version,
        private readonly bool $isTest = false,
    )
    {
    }

    protected function getEndpointBase(): string
    {
        return match ($this->version) {
            self::VERSION_REST_V1 => sprintf('https://api.%skhpos.hu/api/v1.0', $this->isTest ? 'sandbox.' : ''),
        };
    }

    private function prepareEndpointPath(string $endpointPath, array $requestParameters): string
    {
        return preg_replace_callback('/\{([^\}]*)\}/', function (array $matchedElements) use ($requestParameters) {
            $parameterName = $matchedElements[1];
            $value = $requestParameters[$parameterName] ?? null;
            if ($value === null) {
                throw new InvalidArgumentException('Parameter ("%s") not found in request parameters ("%s").', $parameterName, implode('", "', array_keys($requestParameters)));
            }

            return $value;
        }, $endpointPath);
    }

    public function send(RequestInterface $request)
    {
        $requestParameters = $this->getNormalizer()->normalize($request);
        $requestParameters['signature'] = $this->getSignatureProvider()->sign($request->getMerchant(), $requestParameters);

        $endpointPath = $request->getEndpointPath();

        if ($request->getRequestMethod() === 'GET' && \count($requestParameters) > 0) {
            $requestParameters['signature'] = urlencode($requestParameters['signature']);
            $endpointPath = $this->prepareEndpointPath($endpointPath, $requestParameters);
        }

        $httpRequest = $this->getRequestFactory()->createRequest($request->getRequestMethod(), $this->getEndpointBase().$endpointPath);

        if ($request->getRequestMethod() === 'POST' || $request->getRequestMethod() === 'PUT') {
            $bodyString = json_encode($requestParameters, JSON_PRETTY_PRINT);
            $stream = $this->getStreamFactory()->createStream($bodyString);
            $httpRequest = $httpRequest
                ->withHeader('Content-Type', 'application/json')
                ->withBody($stream);
        }

        $response = $this->getHttpClient()->sendRequest($httpRequest);

        if (($statusCode = $response->getStatusCode()) !== 200) {
            $contentType = $response->getHeaders()['content-type'][0] ?? '';

            if ($statusCode === 403 && $contentType !== 'application/json') {
                throw new ClientErrorException($response->getBody()->getContents(), 403);
            }

            if ($contentType !== 'application/json') {
                throw new UnhandledErrorException(sprintf('Unknown (or missing) content type: "%s"', $contentType));
            }

            $responseClass = HttpErrorException::getErrorClassWithResponseCode($statusCode);
        } else {
            $responseClass = $request->getResponseClass();
        }

        $content = $response->getBody()->getContents();

        $responseObject = $this->getSerializer()->deserialize($content, $responseClass, 'json', [
            JsonDecode::ASSOCIATIVE => true,
        ]);

        if ($responseObject instanceof \Exception) {
            throw $responseObject;
        }

        return $responseObject;
    }

    public function getPaymentUrlWithPaymentProcessRequest(PaymentProcessRequest $paymentProcessRequest): string
    {
        $requestParameters = $this->getNormalizer()->normalize($paymentProcessRequest);
        $requestParameters['dttm'] = date("YmdHis");
        $requestParameters['signature'] = urlencode($this->getSignatureProvider()->sign($paymentProcessRequest->getMerchant(), $requestParameters));
        $endpointPath = $this->prepareEndpointPath($paymentProcessRequest->getEndpointPath(), $requestParameters);
        return $this->getEndpointBase().$endpointPath;
    }

    /**
     * @psalm-template Tresponse
     * @param array $responseArray
     * @psalm-param class-string<Tresponse> $responseClass
     * @return Tresponse
     */
    public function initResponseWithArray(array $responseArray, string $responseClass): ResponseInterface
    {
        return $this->getDenormalizer()->denormalize($responseArray, $responseClass, 'array');
    }

    #[SubscribedService]
    private function getSignatureProvider(): SignatureProviderInterface
    {
        return $this->container->get(__METHOD__);
    }

    #[SubscribedService]
    private function getNormalizer(): NormalizerInterface
    {
        return $this->container->get(__METHOD__);
    }

    #[SubscribedService]
    private function getDenormalizer(): DenormalizerInterface
    {
        return $this->container->get(__METHOD__);
    }

    #[SubscribedService]
    private function getSerializer(): SerializerInterface
    {
        return $this->container->get(__METHOD__);
    }

    #[SubscribedService]
    private function getHttpClient(): ClientInterface
    {
        return $this->container->get(__METHOD__);
    }

    #[SubscribedService]
    private function getRequestFactory(): RequestFactoryInterface
    {
        return $this->container->get(__METHOD__);
    }

    #[SubscribedService]
    private function getStreamFactory(): StreamFactoryInterface
    {
        return $this->container->get(__METHOD__);
    }
}
