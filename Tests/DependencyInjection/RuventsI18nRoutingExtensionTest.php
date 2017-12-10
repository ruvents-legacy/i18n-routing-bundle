<?php

declare(strict_types=1);

namespace Ruvents\I18nRoutingBundle\Tests\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Ruvents\I18nRoutingBundle\DependencyInjection\RuventsI18nRoutingExtension;
use Symfony\Component\DependencyInjection\Reference;

class RuventsI18nRoutingExtensionTest extends AbstractExtensionTestCase
{
    public function test(): void
    {
        $this->load([
            'locales' => $locales = ['ru', 'en'],
            'default_locale' => $defaultLocale = 'ru',
        ]);

        $this->assertContainerBuilderHasService('ruvents_i18n_routing.loader_decorator');
        $this->assertContainerBuilderHasServiceDefinitionWithArgument('ruvents_i18n_routing.loader_decorator',
            '$loader', new Reference('ruvents_i18n_routing.loader_decorator.inner'));
        $this->assertContainerBuilderHasServiceDefinitionWithArgument('ruvents_i18n_routing.loader_decorator',
            '$locales', $locales);
        $this->assertContainerBuilderHasServiceDefinitionWithArgument('ruvents_i18n_routing.loader_decorator',
            '$defaultLocale', $defaultLocale);

        $this->assertContainerBuilderHasService('ruvents_i18n_routing.framework_router_decorator');
        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall('ruvents_i18n_routing.framework_router_decorator',
            'setRequestStack', [new Reference('request_stack')]);
        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall('ruvents_i18n_routing.framework_router_decorator',
            'setDefaultLocale', [$defaultLocale]);
    }

    protected function getContainerExtensions()
    {
        return [
            new RuventsI18nRoutingExtension(),
        ];
    }
}
