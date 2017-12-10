<?php

declare(strict_types=1);

namespace Ruvents\I18nRoutingBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;

class RuventsI18nRoutingExtension extends ConfigurableExtension
{
    /**
     * {@inheritdoc}
     */
    public function loadInternal(array $config, ContainerBuilder $container): void
    {
        (new PhpFileLoader($container, new FileLocator(__DIR__.'/../Resources/config')))
            ->load('services.php');

        $container->getDefinition('ruvents_i18n_routing.loader_decorator')
            ->setArgument('$locales', $config['locales'])
            ->setArgument('$defaultLocale', $config['default_locale']);

        $container->getDefinition('ruvents_i18n_routing.framework_router_decorator')
            ->addMethodCall('setDefaultLocale', [$config['default_locale']]);
    }
}
