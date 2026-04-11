<?php declare(strict_types=1);

namespace KHTools\VPos\Bundle\DependencyInjection\Compiler;

use KHTools\VPos\Models\Merchant;
use KHTools\VPos\Payum\Action\AuthorizeAction;
use KHTools\VPos\Payum\Action\CancelAction;
use KHTools\VPos\Payum\Action\CaptureAction;
use KHTools\VPos\Payum\Action\ConvertPaymentAction;
use KHTools\VPos\Payum\Action\RefundAction;
use KHTools\VPos\Payum\Action\StatusAction;
use KHTools\VPos\Payum\Action\SyncAction;
use KHTools\VPos\Payum\KHVPosGatewayFactory;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class PayumGatewayPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition('payum')) {
            return;
        }

        /** @var array<string, mixed> $config */
        $config = $container->getParameter('khvpos.client_provider.config');
        $firstMerchant = reset($config['merchants']);
        if (!isset($firstMerchant['merchant_id']) || $firstMerchant['merchant_id'] === '') {
            throw new \LogicException(
                'PayumGatewayPass requires at least one merchant with a non-empty merchant_id in khvpos.client_provider.config. '
                . 'Note: multi-merchant Payum integration is not yet supported; only the first merchant is used.'
            );
        }
        $merchantId = $firstMerchant['merchant_id'];
        // Only the first merchant is wired into Payum actions. Multi-merchant support
        // (selecting merchant by currency etc.) is not yet implemented for Payum.

        // Register the gateway factory
        $factoryDef = new Definition(KHVPosGatewayFactory::class);
        $factoryDef->addTag('payum.gateway_factory', [
            'factory_name'  => 'khvpos',
            'factory_title' => 'K&H VPos',
        ]);
        $container->setDefinition('khvpos.payum.gateway_factory', $factoryDef);

        $vposClientRef = new Reference('khvpos.vpos_client');

        $merchantDef = new Definition(Merchant::class);
        $merchantDef->addMethodCall('setMerchantId', [$merchantId]);
        $container->setDefinition('khvpos.payum.merchant', $merchantDef);
        $merchantRef = new Reference('khvpos.payum.merchant');

        // Actions that need VPosClient + Merchant
        foreach ([
            'khvpos.payum.action.capture'   => CaptureAction::class,
            'khvpos.payum.action.authorize'  => AuthorizeAction::class,
            'khvpos.payum.action.refund'     => RefundAction::class,
            'khvpos.payum.action.cancel'     => CancelAction::class,
            'khvpos.payum.action.sync'       => SyncAction::class,
        ] as $serviceId => $class) {
            $def = new Definition($class, [$vposClientRef, $merchantRef]);
            $def->addTag('payum.action', ['factory' => 'khvpos']);
            $container->setDefinition($serviceId, $def);
        }

        // Actions with no dependencies
        foreach ([
            'khvpos.payum.action.convert' => ConvertPaymentAction::class,
            'khvpos.payum.action.status'  => StatusAction::class,
        ] as $serviceId => $class) {
            $def = new Definition($class);
            $def->addTag('payum.action', ['factory' => 'khvpos']);
            $container->setDefinition($serviceId, $def);
        }
    }
}
