<?php

namespace Ruwork\RoutingBundle\Routing;

use Ruwork\RoutingBundle\Config\Loader\DecoratingLoader;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Routing\RouteCollection;

class I18nLoader extends DecoratingLoader
{
    /**
     * @var string[]
     */
    private $locales;

    /**
     * @var string
     */
    private $defaultLocale;

    /**
     * @param LoaderInterface $loader
     * @param array           $locales
     * @param string          $defaultLocale
     */
    public function __construct(LoaderInterface $loader, array $locales = [], $defaultLocale)
    {
        parent::__construct($loader);
        $this->locales = $locales;
        $this->defaultLocale = $defaultLocale;
    }

    /**
     * {@inheritdoc}
     */
    public function load($resource, $type = null)
    {
        /** @var RouteCollection $routes */
        $routes = $this->getDecoratedLoader()->load($resource, $type);

        foreach ($routes as $name => $route) {
            if ($route->getOption('i18n') === false) {
                continue;
            }

            $route
                ->setPath('/{_locale}'.ltrim($route->getPath(), '/'))
                ->addDefaults(['_locale' => ''])
                ->addRequirements([
                    '_locale' => implode('|', array_map(function ($locale) {
                        return $locale === $this->defaultLocale ? '' : $locale.'/';
                    }, $this->locales)),
                ]);
        }

        return $routes;
    }
}
