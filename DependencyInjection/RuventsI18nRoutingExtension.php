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
    public function loadInternal(array $config, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(dirname(__DIR__).'/Resources/config'));
        $loader->load('services.yml');

        $container->getDefinition('ruvents_i18n_routing.loader')
            ->replaceArgument(1, $config['locales'])
            ->replaceArgument(2, $config['default_locale']);

        $container->getDefinition('ruvents_i18n_routing.router')
            ->addMethodCall('setDefaultLocale', [$config['default_locale']]);
    }
}
