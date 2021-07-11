<?php

namespace Rabble\WebsiteBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $builder = new TreeBuilder('rabble_website');
        $root = $builder->getRootNode();
        $root
            ->children()
                ->arrayNode('domain_mapping')
                    ->scalarPrototype()->end()
                ->end()
                ->scalarNode('localization_strategy')
                    ->defaultValue('path')->end()
                ->end()
            ->end()
        ;

        return $builder;
    }
}
