<?php

namespace Ruvents\I18nRoutingBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        return (new TreeBuilder())
            ->root('ruwork_i18n_routing')
                ->children()
                    ->arrayNode('locales')
                        ->isRequired()
                        ->requiresAtLeastOneElement()
                        ->prototype('scalar')->cannotBeEmpty()->end()
                    ->end()
                    ->scalarNode('default_locale')
                        ->isRequired()
                    ->end()
                ->end()
            ->end();
    }
}
