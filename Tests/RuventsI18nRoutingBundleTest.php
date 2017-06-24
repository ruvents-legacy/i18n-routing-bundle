<?php

namespace Ruvents\I18nRoutingBundle\Tests;

use PHPUnit\Framework\TestCase;
use Ruvents\I18nRoutingBundle\RuventsI18nRoutingBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class RuventsI18nRoutingBundleTest extends TestCase
{
    public function testBuild()
    {
        $container = new ContainerBuilder();
        $container->register('ruwork_i18n_routing.router', RuventsI18nRoutingBundle::class);
        (new RuventsI18nRoutingBundle())->build($container);
        $container->compile();
        $this->assertTrue($container->hasAlias('router'));
    }
}
