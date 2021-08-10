<?php

declare(strict_types=1);

namespace LML\SDK;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use LML\SDK\DependencyInjection\LMLSDKExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class LMLSDKBundle extends Bundle implements CompilerPassInterface
{
    public function getContainerExtension()
    {
        return $this->extension ?? new LMLSDKExtension();
    }

    public function build(ContainerBuilder $container)
    {
//        $container->addCompilerPass($this);
    }

    public function process(ContainerBuilder $container)
    {
        $client = $container->getDefinition('lml_sdk.client');
        $repos = $container->findTaggedServiceIds('lml_sdk.repository');
        foreach ($repos as $id => $_repo) {
            $container->getDefinition($id)->addMethodCall('setClient', [$client]);
        }

    }
}
