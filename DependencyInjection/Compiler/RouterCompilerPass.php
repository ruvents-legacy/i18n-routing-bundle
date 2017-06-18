<?php

namespace Ruvents\I18nRoutingBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class RouterCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasAlias('router') || !$container->has('ruwork_i18n_routing.router')) {
            return;
        }

        $container->setAlias('router', 'ruwork_i18n_routing.router');
    }
}
