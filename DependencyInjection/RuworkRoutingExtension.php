<?php

namespace Ruwork\RoutingBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class RuworkRoutingExtension extends ConfigurableExtension
{
    /**
     * {@inheritdoc}
     */
    public function loadInternal(array $mergedConfig, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(dirname(__DIR__).'/Resources/config')
        );

        $loader->load('services.yml');

        $container->findDefinition('ruwork_routing.i18n_router')
            ->addMethodCall('setDefaultLocale', [$mergedConfig['i18n']['default_locale']]);

        $container->findDefinition('ruwork_routing.i18n_loader')
            ->replaceArgument(1, $mergedConfig['i18n']['locales'])
            ->replaceArgument(2, $mergedConfig['i18n']['default_locale']);

        $container->findDefinition('ruwork_routing.template_loader')
            ->replaceArgument(2, $mergedConfig['i18n']['locales']);
    }
}
