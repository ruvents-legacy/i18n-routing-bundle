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
        $rootNode = $treeBuilder->root('ruwork_routing');

        $rootNode
            ->children()
                ->arrayNode('i18n')
                    ->canBeEnabled()
                    ->children()
                        ->arrayNode('locales')
                            ->isRequired()
                            ->prototype('scalar')->cannotBeEmpty()->end()
                        ->end()
                        ->scalarNode('default_locale')->isRequired()->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
