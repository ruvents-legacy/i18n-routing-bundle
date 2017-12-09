<?php

namespace Ruvents\I18nRoutingBundle\Routing;

use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class I18nFrameworkRouterDecorator extends Router
{
    /**
     * @var string
     */
    private $defaultLocale = 'en';

    /**
     * @var null|RequestStack
     */
    private $requestStack;

    public function setDefaultLocale(string $defaultLocale): void
    {
        $this->defaultLocale = $defaultLocale;
    }

    public function setRequestStack(RequestStack $requestStack): void
    {
        $this->requestStack = $requestStack;
    }

    /**
     * {@inheritdoc}
     */
    public function generate($name, $parameters = [], $referenceType = self::ABSOLUTE_PATH)
    {
        $this->preGenerate($name, $parameters);

        return parent::generate($name, $parameters, $referenceType);
    }

    /**
     * {@inheritdoc}
     */
    public function match($pathinfo)
    {
        $parameters = parent::match($pathinfo);
        $this->postMatch($parameters);

        return $parameters;
    }

    /**
     * {@inheritdoc}
     */
    public function matchRequest(Request $request)
    {
        $parameters = parent::matchRequest($request);
        $this->postMatch($parameters);

        return $parameters;
    }

    private function preGenerate(string $name, array &$parameters): void
    {
        if (null === $route = $this->getRouteCollection()->get($name)) {
            return;
        }

        if (false === $route->getOption('i18n')) {
            return;
        }

        $locale = $this->getLocale($parameters);
        $parameters['_locale'] = $this->defaultLocale === $locale ? '' : $locale.'/';
    }

    private function getLocale(array $parameters): string
    {
        if (isset($parameters['_locale'])) {
            return $parameters['_locale'];
        }

        if (null === $this->requestStack) {
            return $this->defaultLocale;
        }

        if (null === $request = $this->requestStack->getCurrentRequest()) {
            return $this->defaultLocale;
        }

        return $request->getLocale();
    }

    private function postMatch(array &$parameters): void
    {
        if (!isset($parameters['_route']) || !isset($parameters['_locale'])) {
            return;
        }

        if (null === $route = $this->getRouteCollection()->get($parameters['_route'])) {
            return;
        }

        if (false === $route->getOption('i18n')) {
            return;
        }

        $parameters['_locale'] = isset($parameters['_locale']) && '' !== $parameters['_locale']
            ? rtrim($parameters['_locale'], '/')
            : $this->defaultLocale;
    }
}
