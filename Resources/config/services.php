<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Ruvents\I18nRoutingBundle\Routing\I18nFrameworkRouterDecorator;
use Ruvents\I18nRoutingBundle\Routing\I18nLoaderDecorator;

return function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->set('ruvents_i18n_routing.loader_decorator', I18nLoaderDecorator::class)
        ->private()
        ->decorate('routing.loader')
        ->args([
            '$loader' => ref('ruvents_i18n_routing.loader_decorator.inner'),
        ]);

    $services->set('ruvents_i18n_routing.framework_router_decorator', I18nFrameworkRouterDecorator::class)
        ->private()
        ->parent('router.default')
        ->call('setRequestStack', [ref('request_stack')]);
};
