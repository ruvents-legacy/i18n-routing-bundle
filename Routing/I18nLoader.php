<?php

namespace Ruwork\RoutingBundle\Routing;

use Ruwork\RoutingBundle\Config\Loader\DecoratingLoader;
use Symfony\Component\Routing\RouteCollection;

class I18nLoader extends DecoratingLoader
{
    /**
     * {@inheritdoc}
     */
    public function load($resource, $type = null)
    {
        /** @var RouteCollection $routes */
        $routes = $this->getDecoratedLoader()->load($resource, $type);

        foreach ($routes as $name => $route) {
            if ($route->hasOption('i18n') && $route->getOption('i18n') === false) {
                continue;
            }

            $route
                ->setPath('/{_locale}'.ltrim($route->getPath(), '/'))
                ->addDefaults(['_locale' => ''])
                ->addRequirements(['_locale' => '|en/']);
        }

        return $routes;
    }
}
