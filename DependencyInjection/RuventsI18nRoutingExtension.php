<?php

namespace Ruvents\I18nRoutingBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;

class RuventsI18nRoutingExtension extends ConfigurableExtension
{
    /**
     * {@inheritdoc}
     */
    public function loadInternal(array $mergedConfig, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(dirname(__DIR__).'/Resources/config'));
        $loader->load('services.yml');

        $container->getDefinition('ruwork_i18n_routing.loader')
            ->replaceArgument(1, $mergedConfig['locales'])
            ->replaceArgument(2, $mergedConfig['default_locale']);

        $container->getDefinition('ruwork_i18n_routing.router')
            ->addMethodCall('setDefaultLocale', [$mergedConfig['default_locale']]);
    }
}
