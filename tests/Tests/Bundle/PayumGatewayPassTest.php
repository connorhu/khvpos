<?php declare(strict_types=1);

namespace KHTools\Tests\Bundle;

use KHTools\VPos\Bundle\DependencyInjection\Compiler\PayumGatewayPass;
use KHTools\VPos\Payum\Action\AuthorizeAction;
use KHTools\VPos\Payum\Action\CancelAction;
use KHTools\VPos\Payum\Action\CaptureAction;
use KHTools\VPos\Payum\Action\ConvertPaymentAction;
use KHTools\VPos\Payum\Action\RefundAction;
use KHTools\VPos\Payum\Action\StatusAction;
use KHTools\VPos\Payum\Action\SyncAction;
use KHTools\VPos\Payum\KHVPosGatewayFactory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class PayumGatewayPassTest extends TestCase
{
    public function testIsNoopWhenPayumServiceAbsent(): void
    {
        $container = new ContainerBuilder();
        // No 'payum' service registered
        $pass = new PayumGatewayPass();
        $pass->process($container);
        // Should not throw and should not add any definitions
        $this->assertFalse($container->hasDefinition('khvpos.payum.gateway_factory'));
    }

    public function testRegistersGatewayFactoryAndActionsWhenPayumPresent(): void
    {
        $container = new ContainerBuilder();
        $container->setDefinition('payum', new Definition(\stdClass::class));
        $container->setDefinition('khvpos.vpos_client', new Definition(\stdClass::class));
        $container->setParameter('khvpos.client_provider.config', [
            'merchants' => [['merchant_id' => 'M123']],
        ]);

        $pass = new PayumGatewayPass();
        $pass->process($container);

        $this->assertTrue($container->hasDefinition('khvpos.payum.gateway_factory'));

        $factoryDef = $container->getDefinition('khvpos.payum.gateway_factory');
        $tags = $factoryDef->getTags();
        $this->assertArrayHasKey('payum.gateway_factory', $tags);
        $this->assertSame('khvpos', $tags['payum.gateway_factory'][0]['factory_name']);

        // Verify action services are registered
        foreach ([
            'khvpos.payum.action.convert'   => ConvertPaymentAction::class,
            'khvpos.payum.action.capture'    => CaptureAction::class,
            'khvpos.payum.action.authorize'  => AuthorizeAction::class,
            'khvpos.payum.action.status'     => StatusAction::class,
            'khvpos.payum.action.refund'     => RefundAction::class,
            'khvpos.payum.action.cancel'     => CancelAction::class,
            'khvpos.payum.action.sync'       => SyncAction::class,
        ] as $serviceId => $class) {
            $this->assertTrue($container->hasDefinition($serviceId), "Missing service: $serviceId");
            $def = $container->getDefinition($serviceId);
            $this->assertSame($class, $def->getClass());
            $actionTags = $def->getTags();
            $this->assertArrayHasKey('payum.action', $actionTags);
            $this->assertSame('khvpos', $actionTags['payum.action'][0]['factory'] ?? null);
        }
    }
}
