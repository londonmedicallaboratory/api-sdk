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
            ->scalarNode('base_url')->defaultValue('')->isRequired()->end()
            ?->scalarNode('username')->defaultValue('')->isRequired()->end()
            ?->scalarNode('password')->defaultValue('')->isRequired()->end()
            ?->scalarNode('cache_pool')->defaultValue(null)->end()
            ?->integerNode('cache_expiration')->defaultValue(0)->end()
            ?->end();

        return $treeBuilder;
    }
}
