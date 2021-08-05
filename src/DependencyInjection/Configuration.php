<?php

declare(strict_types=1);

namespace LML\SDK\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('lml_sdk');
        $treeBuilder->getRootNode()
            ->children()
            ->scalarNode('base_url')->isRequired()->end()
            ?->scalarNode('username')->isRequired()->end()
            ?->scalarNode('password')->isRequired()->end()
            ?->end();

        return $treeBuilder;
    }
}
