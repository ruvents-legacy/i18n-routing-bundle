<?php

namespace Ruvents\I18nRoutingBundle\Tests\Routing;

use PHPUnit\Framework\TestCase;
use Ruvents\I18nRoutingBundle\Routing\I18nLoader;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\LoaderResolver;

class I18nLoaderTest extends TestCase
{
    public function testDecorated()
    {
        $mockLoader = $this->getMockBuilder(LoaderInterface::class)->getMock();

        $mockLoader->expects($this->once())
            ->method('load')
            ->with($this->equalTo($resource = 'res'), $this->equalTo($type = 'type'))
            ->willReturn($loadResult = 'loaded');

        $mockLoader->expects($this->once())
            ->method('supports')
            ->with($this->equalTo($resource), $this->equalTo($type))
            ->willReturn(true);

        $mockLoader->expects($this->once())
            ->method('getResolver')
            ->willReturn($resolver = new LoaderResolver());

        $mockLoader->expects($this->once())
            ->method('setResolver')
            ->with($this->equalTo($resolver))
            ->willReturnSelf();

        /** @var LoaderInterface $mockLoader */

        $loader = new I18nLoader($mockLoader, $ls = ['ru', 'en'], $dl = 'ru');

        // test simply decorated methods
        $this->assertEquals($loadResult, $loader->load($resource, $type));
        $this->assertTrue($loader->supports($resource, $type));
        $this->assertEquals($resolver, $loader->getResolver());
        $this->assertEquals($mockLoader, $loader->setResolver($resolver));
    }
}
