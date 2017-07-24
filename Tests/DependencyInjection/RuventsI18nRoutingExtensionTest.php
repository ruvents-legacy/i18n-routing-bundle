<?php

namespace Ruvents\I18nRoutingBundle\Tests\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Ruvents\I18nRoutingBundle\DependencyInjection\RuventsI18nRoutingExtension;
use Symfony\Component\DependencyInjection\Reference;

class RuventsI18nRoutingExtensionTest extends AbstractExtensionTestCase
{
    public function test()
    {
        $this->load([
            'locales' => $locales = ['ru', 'en'],
            'default_locale' => $defaultLocale = 'ru',
        ]);

        $this->assertContainerBuilderHasService('ruvents_i18n_routing.loader');
        $this->assertContainerBuilderHasServiceDefinitionWithArgument('ruvents_i18n_routing.loader',
            0, new Reference('ruvents_i18n_routing.loader.inner'));
        $this->assertContainerBuilderHasServiceDefinitionWithArgument('ruvents_i18n_routing.loader',
            1, $locales);
        $this->assertContainerBuilderHasServiceDefinitionWithArgument('ruvents_i18n_routing.loader',
            2, $defaultLocale);

        $this->assertContainerBuilderHasService('ruvents_i18n_routing.router');
        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall('ruvents_i18n_routing.router',
            'setRequestStack', [new Reference('request_stack')]);
        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall('ruvents_i18n_routing.router',
            'setDefaultLocale', [$defaultLocale]);
    }

    protected function getContainerExtensions()
    {
        return [
            new RuventsI18nRoutingExtension(),
        ];
    }
}
