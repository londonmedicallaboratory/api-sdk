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
            ->scalarNode('base_url')->defaultValue('')->end()
            ?->scalarNode('api_token')->defaultValue('')->end()
            ?->scalarNode('cache_pool')->defaultValue(null)->end()
            ?->integerNode('cache_expiration')->defaultValue(0)->end()
            ?->booleanNode('faker')->defaultValue(false)->end()
            ?->scalarNode('loqate_api_key')->isRequired()->end()
            ?->end();

        return $treeBuilder;
    }
}
