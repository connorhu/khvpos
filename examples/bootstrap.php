<?php

use KHTools\VPos\Normalizers\AddressNormalizer;
use KHTools\VPos\Normalizers\CartItemNormalizer;
use KHTools\VPos\Normalizers\EnumNormalizer;
use KHTools\VPos\Normalizers\HttpErrorNormalizer;
use KHTools\VPos\Normalizers\RequestNormalizer;
use KHTools\VPos\Normalizers\ResponseNormalizer;
use KHTools\VPos\SignatureProvider;
use KHTools\VPos\SignatureProviderInterface;
use KHTools\VPos\VPosClient;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\HttpClient\Psr18Client;
use Symfony\Component\Serializer;

require_once __DIR__.'/../vendor/autoload.php';

(new Dotenv())->loadEnv(__DIR__.'/.env');

$container = new ContainerBuilder();

/**
 * vpos client init
 */
$definition = (new Definition(VPosClient::class))
    ->setAutowired(true)
    ->setPublic(true)
    ->setArguments([
        VPosClient::VERSION_REST_V1,
        true
    ])
    ->addTag('container.service_subscriber');
$container->setDefinition(VPosClient::class, $definition);

/**
 * http client init
 */
$definition = new Definition(Psr18Client::class);
$container->setDefinition(ClientInterface::class, $definition);
$container->setAlias(RequestFactoryInterface::class, ClientInterface::class);
$container->setAlias(StreamFactoryInterface::class, ClientInterface::class);

/**
 * signature provider init
 */
$definition = (new Definition(SignatureProvider::class))
    ->setArguments([
        [],
        __DIR__.'/../devtools/mips_pay.sandbox.khpos.hu.pub',
    ])
    ->addMethodCall('addPrivateKey', [$_ENV['MERCHANT_ID'], $_ENV['PRIVATE_KEY'], $_ENV['PRIVATE_KEY_PASSPHRASE']]);
$container->setDefinition(SignatureProviderInterface::class, $definition);

/**
 * object normalizer init
 */
$definition = (new Definition(Serializer\Mapping\Loader\AttributeLoader::class))
    ->setAutowired(true);
$container->setDefinition(Serializer\Mapping\Loader\LoaderInterface::class, $definition);

$definition = (new Definition(Serializer\Mapping\Factory\ClassMetadataFactory::class))
    ->setAutowired(true);
$container->setDefinition(Serializer\Mapping\Factory\ClassMetadataFactoryInterface::class, $definition);

$definition = (new Definition(Serializer\NameConverter\MetadataAwareNameConverter::class))
    ->setAutowired(true)
    ->setArgument(1, null);
$container->setDefinition(Serializer\NameConverter\NameConverterInterface::class, $definition);

$definition = (new Definition(Serializer\Normalizer\ObjectNormalizer::class))
    ->setAutowired(true);
$container->setDefinition(Serializer\Normalizer\ObjectNormalizer::class, $definition);

$definition = (new Definition(Serializer\Serializer::class))
    ->setAutowired(true)
    ->setPublic(true)
    ->setArgument(0, [])
    ->setArgument(1, []);
$container->setDefinition('serializer', $definition);
$container->setAlias(Serializer\Serializer::class, 'serializer');
$container->setAlias(Serializer\SerializerInterface::class, Serializer\Serializer::class);
$container->setAlias(Serializer\Normalizer\NormalizerInterface::class, Serializer\Serializer::class);
$container->setAlias(Serializer\Normalizer\DenormalizerInterface::class, Serializer\Serializer::class);

$definition = new Definition(Serializer\Encoder\JsonEncoder::class);
$definition->addTag('serializer.encoder');
$container->setDefinition(Serializer\Encoder\JsonEncoder::class, $definition);

$definition = (new Definition(Serializer\Normalizer\DateTimeNormalizer::class))
    ->addTag('serializer.normalizer');
$container->setDefinition(Serializer\Normalizer\DateTimeNormalizer::class, $definition);

$definition = (new Definition(AddressNormalizer::class))
    ->setAutowired(true)
    ->addTag('serializer.normalizer');
$container->setDefinition(AddressNormalizer::class, $definition);

$definition = (new Definition(CartItemNormalizer::class))
    ->setAutowired(true)
    ->addTag('serializer.normalizer');
$container->setDefinition(CartItemNormalizer::class, $definition);

$definition = (new Definition(EnumNormalizer::class))
    ->addTag('serializer.normalizer');
$container->setDefinition(EnumNormalizer::class, $definition);

$definition = (new Definition(HttpErrorNormalizer::class))
    ->addTag('serializer.normalizer');
$container->setDefinition(HttpErrorNormalizer::class, $definition);

$definition = (new Definition(RequestNormalizer::class))
    ->setAutowired(true)
    ->addTag('serializer.normalizer');
$container->setDefinition(RequestNormalizer::class, $definition);

$definition = (new Definition(ResponseNormalizer::class))
    ->setAutowired(true)
    ->addTag('serializer.normalizer');
$container->setDefinition(ResponseNormalizer::class, $definition);

$container->addCompilerPass(new Serializer\DependencyInjection\SerializerPass()); // it collects all "serializer.normalizer" tagged normalizers
$container->setParameter('kernel.debug', false); // because of SerializerPass

$container->compile();

return $container->get(VPosClient::class);
