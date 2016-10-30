<?php

namespace Ruwork\RoutingBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
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

        $i18n = $mergedConfig['i18n'];

        if (!in_array($i18n['default_locale'], $i18n['locales'], true)) {
            throw new InvalidConfigurationException('The path "ruwork_routing.i18n.locales" must contain "ruwork_routing.i18n.default_locale" value.');
        }

        $container->findDefinition('ruwork_routing.i18n_router')
            ->addMethodCall('setDefaultLocale', [$i18n['default_locale']]);

        $container->findDefinition('ruwork_routing.i18n_loader')
            ->replaceArgument(1, $i18n['locales'])
            ->replaceArgument(2, $i18n['default_locale']);

        $container->findDefinition('ruwork_routing.template_loader')
            ->replaceArgument(2, $i18n['locales']);
    }
}
