<?php

declare(strict_types=1);

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
            ->root('ruvents_i18n_routing')
                ->children()
                    ->arrayNode('locales')
                        ->isRequired()
                        ->requiresAtLeastOneElement()
                        ->scalarPrototype()
                            ->cannotBeEmpty()
                        ->end()
                    ->end()
                    ->scalarNode('default_locale')
                        ->isRequired()
                        ->cannotBeEmpty()
                    ->end()
                ->end()
            ->end();
    }
}
