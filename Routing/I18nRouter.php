<?php

namespace Ruvents\I18nRoutingBundle\Routing;

use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

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
     *
     * @return $this
     */
    public function setDefaultLocale($defaultLocale)
    {
        $this->defaultLocale = $defaultLocale;

        return $this;
    }

    /**
     * @param RequestStack $requestStack
     *
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
        $this->beforeGenerate($name, $parameters);

        return parent::generate($name, $parameters, $referenceType);
    }

    /**
     * {@inheritdoc}
     */
    public function match($pathinfo)
    {
        $parameters = parent::match($pathinfo);

        $this->afterMatch($parameters);

        return $parameters;
    }

    /**
     * {@inheritdoc}
     */
    public function matchRequest(Request $request)
    {
        $parameters = parent::matchRequest($request);

        $this->afterMatch($parameters);

        return $parameters;
    }

    /**
     * @param string $name
     * @param array  $parameters
     */
    private function beforeGenerate($name, array &$parameters = [])
    {
        if (null === $route = $this->getRouteCollection()->get($name)) {
            return;
        }

        if ($route->getOption('i18n') === false) {
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
     * @param array $parameters
     */
    private function afterMatch(array &$parameters = [])
    {
        if (!isset($parameters['_route']) || !isset($parameters['_locale'])) {
            return;
        }

        if (null === $route = $this->getRouteCollection()->get($parameters['_route'])) {
            return;
        }

        if ($route->getOption('i18n') === false) {
            return;
        }

        $parameters['_locale'] = empty($parameters['_locale'])
            ? $this->defaultLocale
            : rtrim($parameters['_locale'], '/');
    }
}
