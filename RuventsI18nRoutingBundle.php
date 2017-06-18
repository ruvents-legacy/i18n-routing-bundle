<?php

namespace Ruvents\I18nRoutingBundle;

use Ruvents\I18nRoutingBundle\DependencyInjection\Compiler\RouterCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class RuventsI18nRoutingBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new RouterCompilerPass());
    }
}
