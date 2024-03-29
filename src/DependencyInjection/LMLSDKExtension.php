<?php

declare(strict_types=1);

namespace LML\SDK\DependencyInjection;

use LML\SDK\Form\Type\AddressType;
use LML\SDK\Service\API\EntityManager;
use LML\SDK\Service\Client\FakerClient;
use Symfony\Component\Config\FileLocator;
use LML\SDK\Service\API\AbstractRepository;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use LML\SDK\Service\Payment\Strategy\PaymentProcessorStrategyInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;
use Symfony\Component\DependencyInjection\Compiler\PriorityTaggedServiceTrait;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;

class LMLSDKExtension extends ConfigurableExtension implements CompilerPassInterface, PrependExtensionInterface
{
    use PriorityTaggedServiceTrait;

    public function getAlias(): string
    {
        return 'lml_sdk';
    }

    public function process(ContainerBuilder $container): void
    {
        $repos = $container->findTaggedServiceIds('lml_sdk.repository');
        foreach ($repos as $id => $_repo) {
            $definition = $container->getDefinition($id);
            $definition->addMethodCall('setClient', [new Reference('lml_sdk.client')]);
            $definition->addMethodCall('setEntityManager', [new Reference(EntityManager::class)]);
        }
    }

    public function prepend(ContainerBuilder $container): void
    {
        $bundles = $container->getParameter('kernel.bundles');

        if (!isset($bundles['TwigBundle'])) {
            return;
        }

        $container->prependExtensionConfig('twig', [
            'form_themes' => [
                '@LMLSDK/forms/calendar_widget.html.twig',
                '@LMLSDK/forms/address_widget.html.twig',
            ],
        ]);
    }

    protected function loadInternal(array $mergedConfig, ContainerBuilder $container): void
    {
        $container->registerForAutoconfiguration(AbstractRepository::class)
            ->addTag('lml_sdk.repository');
        $container->registerForAutoconfiguration(PaymentProcessorStrategyInterface::class)
            ->addTag('lml_sdk.payment_strategy');

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.xml');
        if ($container->getParameter('kernel.debug') === true) {
            $loader->load('profiler.xml');
        }

        $this->configureClient($mergedConfig, $container);
        $this->configureAddressType($mergedConfig, $container);
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
            ->setArgument(1, $mergedConfig['api_token'])
            ->setArgument(2, $mergedConfig['cache_pool'] ? new Reference($mergedConfig['cache_pool']) : null)
            ->setArgument(3, $mergedConfig['cache_expiration']);
    }

    private function configureAddressType(array $mergedConfig, ContainerBuilder $container): void
    {
        $definition = $container->getDefinition(AddressType::class);
        $definition->setArgument(0, $mergedConfig['loqate_api_key']);
    }
}
