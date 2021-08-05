<?php

declare(strict_types=1);

namespace LML\SDK\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use LML\SDK\Repository\RepositoryInterface;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;
use Symfony\Component\DependencyInjection\Compiler\PriorityTaggedServiceTrait;
use function str_starts_with;

class LMLSDKExtension extends ConfigurableExtension implements CompilerPassInterface
{
    use PriorityTaggedServiceTrait;

    public function getAlias()
    {
        return 'lml_sdk';
    }

    protected function loadInternal(array $mergedConfig, ContainerBuilder $container)
    {
        $container->registerForAutoconfiguration(RepositoryInterface::class)->addTag('lml_sdk.repository')->addMethodCall('asdasd', [new Reference('lml_api.client')]);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.xml');

        $container->getDefinition('lml_api.client')
            ->setArgument(0, $mergedConfig['base_url'])
            ->setArgument(1, $mergedConfig['username'])
            ->setArgument(2, $mergedConfig['password']);

        $this->configureTestEnvironment($container);
    }

    /**
     * Make all services public when env=test
     */
    private function configureTestEnvironment(ContainerBuilder $container): void
    {
        if ($container->getParameter('kernel.environment') !== 'test') {
            return;
        }
        foreach ($container->getDefinitions() as $id => $definition) {
            if (str_starts_with($id, 'lml_api')) {
                $definition->setPublic(true);
            }
        }
    }

    public function process(ContainerBuilder $container)
    {
        $repos = $container->findTaggedServiceIds('lml_sdk.repository');
        foreach ($repos as $id => $_repo) {
            $definition = $container->getDefinition($id);
            $definition->addMethodCall('setClient', [new Reference('lml_api.client')]);
            $definition->addMethodCall('setIdentityMap', [new Reference('lml_api.identity_map')]);
        }
    }
}
