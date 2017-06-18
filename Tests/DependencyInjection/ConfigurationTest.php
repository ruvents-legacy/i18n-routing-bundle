<?php

namespace Ruvents\I18nRoutingBundle\Tests\DependencyInjection;

use Matthias\SymfonyConfigTest\PhpUnit\ConfigurationTestCaseTrait;
use Ruvents\I18nRoutingBundle\DependencyInjection\Configuration;

class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    use ConfigurationTestCaseTrait;

    public function testEmpty()
    {
        $this->assertConfigurationIsInvalid([]);
    }

    public function testArray()
    {
        $this->assertProcessedConfigurationEquals(
            [
                'ruwork_i18n_routing' => [
                    'locales' => ['ru'],
                    'default_locale' => 'ru',
                ],
            ],
            [
                'locales' => ['ru'],
                'default_locale' => 'ru',
            ]
        );
    }

    protected function getConfiguration()
    {
        return new Configuration();
    }
}
