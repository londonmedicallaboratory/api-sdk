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
            ->scalarNode('base_url')->defaultNull()->end()
            ?->scalarNode('api_token')->defaultNull()->end()
            ?->scalarNode('cache_pool')->defaultNull()->end()
            ?->integerNode('cache_expiration')->defaultValue(0)->end()
            ?->booleanNode('faker')->defaultValue(false)->end()
            ?->end();

        return $treeBuilder;
    }
}
