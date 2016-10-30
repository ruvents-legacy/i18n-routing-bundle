<?php

namespace Ruwork\RoutingBundle\Routing;

use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Route;

class I18nRouter extends Router
{
    /**
     * @var string
     */
    private $defaultLocale;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @param string $defaultLocale
     * @return $this
     */
    public function setDefaultLocale($defaultLocale)
    {
        $this->defaultLocale = $defaultLocale;

        return $this;
    }

    /**
     * @param RequestStack $requestStack
     * @return $this
     */
    public function setRequestStack(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function generate($name, $parameters = [], $referenceType = self::ABSOLUTE_PATH)
    {
        $this->beforeGenerate($this->getRouteCollection()->get($name), $parameters);

        return parent::generate($name, $parameters, $referenceType);
    }

    /**
     * {@inheritdoc}
     */
    public function match($pathinfo)
    {
        $parameters = parent::match($pathinfo);

        $this->afterMatch($parameters, $this->getRouteCollection()->get($parameters['_route']));

        return $parameters;
    }

    /**
     * {@inheritdoc}
     */
    public function matchRequest(Request $request)
    {
        $parameters = parent::matchRequest($request);

        $this->afterMatch($parameters, $this->getRouteCollection()->get($parameters['_route']));

        return $parameters;
    }

    /**
     * @param Route|null $route
     * @param array      $parameters
     */
    private function beforeGenerate(Route $route = null, array &$parameters)
    {
        if ($route === null || $route->getOption('i18n') === false) {
            return;
        }

        if (!isset($parameters['_locale'])) {
            $masterRequest = $this->requestStack->getMasterRequest();

            $locale = $masterRequest === null
                ? $this->defaultLocale
                : $masterRequest->getLocale();
        } else {
            $locale = $parameters['_locale'];
        }

        $parameters['_locale'] = $locale === $this->defaultLocale
            ? ''
            : $locale.'/';
    }

    /**
     * @param array      $parameters
     * @param Route|null $route
     */
    private function afterMatch(array &$parameters, Route $route = null)
    {
        if (!isset($parameters['_locale']) || $route->getOption('i18n') === false) {
            return;
        }

        $parameters['_locale'] = empty($parameters['_locale'])
            ? $this->defaultLocale
            : rtrim($parameters['_locale'], '/');
    }
}
