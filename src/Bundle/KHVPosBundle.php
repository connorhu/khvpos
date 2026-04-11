<?php declare(strict_types=1);

namespace KHTools\VPos\Bundle;

use KHTools\VPos\Bundle\DependencyInjection\PaymentGatewayExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class KHVPosBundle extends Bundle
{
    public function getContainerExtension(): ?ExtensionInterface
    {
        return new PaymentGatewayExtension();
    }
}
