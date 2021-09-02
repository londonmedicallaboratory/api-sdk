<?php

declare(strict_types=1);

namespace LML\SDK\DependencyInjection;

use LML\SDK\Service\Client\FakerClient;
use Symfony\Component\Config\FileLocator;
use LML\SDK\ViewFactory\AbstractViewRepository;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use LML\SDK\Service\Payment\Strategy\PaymentProcessorStrategyInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;
use Symfony\Component\DependencyInjection\Compiler\PriorityTaggedServiceTrait;

class LMLSDKExtension extends ConfigurableExtension implements CompilerPassInterface
{
    use PriorityTaggedServiceTrait;

    public function getAlias()
    {
        return 'lml_sdk';
    }

    public function process(ContainerBuilder $container): void
    {
        $repos = $container->findTaggedServiceIds('lml_sdk.repository');
        foreach ($repos as $id => $_repo) {
            $definition = $container->getDefinition((string)$id);
            $definition->addMethodCall('setClient', [new Reference('lml_sdk.client')]);
        }
    }

    protected function loadInternal(array $mergedConfig, ContainerBuilder $container): void
    {
        $container->registerForAutoconfiguration(AbstractViewRepository::class)
            ->addTag('lml_sdk.repository');
        $container->registerForAutoconfiguration(PaymentProcessorStrategyInterface::class)
            ->addTag('lml_sdk.payment_strategy');

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.xml');
        if ($container->getParameter('kernel.debug') === true) {
            $loader->load('profiler.xml');
        }

        $this->configureClient($mergedConfig, $container);
    }

    private function configureClient(array $mergedConfig, ContainerBuilder $container): void
    {
        $definition = $container->getDefinition('lml_sdk.client');

        if ($mergedConfig['faker']) {
            $definition
                ->setClass(FakerClient::class);

            return;
        }

        $definition
            ->setArgument(0, $mergedConfig['base_url'])
            ->setArgument(1, $mergedConfig['username'])
            ->setArgument(2, $mergedConfig['password'])
            ->setArgument(3, $mergedConfig['cache_pool'] ? new Reference($mergedConfig['cache_pool']) : null)
            ->setArgument(4, $mergedConfig['cache_expiration']);
    }
}
