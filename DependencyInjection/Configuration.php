<?php

namespace Ruwork\RoutingBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();

        $treeBuilder
            ->root('ruwork_routing')
                ->children()
                    ->arrayNode('i18n')
                        ->children()
                            ->arrayNode('locales')
                                ->isRequired()
                                ->requiresAtLeastOneElement()
                                ->prototype('scalar')->cannotBeEmpty()->end()
                            ->end()
                            ->scalarNode('default_locale')->isRequired()->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
