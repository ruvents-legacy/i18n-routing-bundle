<?php

namespace Ruwork\RoutingBundle\Routing;

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\LoaderResolverInterface;
use Symfony\Component\Routing\RouteCollection;

class I18nLoader implements LoaderInterface
{
    /**
     * @var LoaderInterface
     */
    private $loader;

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
        $this->loader = $loader;
        $this->locales = $locales;
        $this->defaultLocale = $defaultLocale;
    }

    /**
     * {@inheritdoc}
     */
    public function load($resource, $type = null)
    {
        /** @var RouteCollection $routes */
        $routes = $this->loader->load($resource, $type);

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

    /**
     * {@inheritdoc}
     */
    public function supports($resource, $type = null)
    {
        return $this->loader->supports($resource, $type);
    }

    /**
     * {@inheritdoc}
     */
    public function getResolver()
    {
        return $this->loader->getResolver();
    }

    /**
     * {@inheritdoc}
     */
    public function setResolver(LoaderResolverInterface $resolver)
    {
        return $this->loader->setResolver($resolver);
    }
}
