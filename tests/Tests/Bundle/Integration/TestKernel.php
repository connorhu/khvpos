<?php declare(strict_types=1);

namespace KHTools\Tests\Bundle\Integration;

use KHTools\VPos\Bundle\KHVPosBundle;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpClient\Psr18Client;
use Symfony\Component\HttpKernel\Kernel;

class TestKernel extends Kernel
{
    use MicroKernelTrait;

    public function registerBundles(): iterable
    {
        yield new FrameworkBundle();
        yield new KHVPosBundle();
    }

    protected function configureContainer(ContainerConfigurator $container): void
    {
        $container->extension('framework', [
            'test' => true,
            'secret' => 'test_secret',
            'http_client' => true,
        ]);

        $container->extension('khvpos', [
            'test' => true,
            'merchants' => [
                'default' => [
                    'currency' => 'HUF',
                    'merchant_id' => 'M123456789',
                    'private_key_path' => __DIR__ . '/../../Fixtures/test1_private_key.pem',
                    'private_key_passphrase' => '',
                ],
            ],
        ]);

        $services = $container->services();

        $services->set('psr17_factory', Psr17Factory::class);
        $services->alias(RequestFactoryInterface::class, 'psr17_factory');
        $services->alias(StreamFactoryInterface::class, 'psr17_factory');

        $services->set('psr18_client', Psr18Client::class)
            ->autowire(true);
        $services->alias(ClientInterface::class, 'psr18_client');
    }

    public function getCacheDir(): string
    {
        return sys_get_temp_dir() . '/khvpos_bundle_test_cache';
    }

    public function getLogDir(): string
    {
        return sys_get_temp_dir() . '/khvpos_bundle_test_log';
    }
}
