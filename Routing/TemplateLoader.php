<?php

namespace Ruwork\RoutingBundle\Routing;

use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Bundle\FrameworkBundle\Templating\TemplateNameParser;
use Symfony\Bundle\FrameworkBundle\Templating\TemplateReference;
use Ruwork\RoutingBundle\Config\Resource\RegexFileStructureResource;
use Ruwork\RoutingBundle\Templating\TemplateExportableReference;
use Ruwork\RoutingBundle\Templating\I18nTemplateReference;

class TemplateLoader extends Loader
{
    const NAME = 'ruwork_template';

    const ROUTE_PARAM = 'i18nTemplate';

    /**
     * @var TemplateNameParser
     */
    private $templateNameParser;

    /**
     * @var FileLocatorInterface
     */
    private $fileLocator;

    /**
     * @var string[]
     */
    private $loadedResources = [];

    /**
     * @var string[]
     */
    private $locales;

    /**
     * @param TemplateNameParser   $templateNameParser
     * @param FileLocatorInterface $fileLocator
     * @param string[]             $locales
     */
    public function __construct(
        TemplateNameParser $templateNameParser,
        FileLocatorInterface $fileLocator,
        array $locales
    ) {
        $this->templateNameParser = $templateNameParser;
        $this->fileLocator = $fileLocator;
        $this->locales = $locales;
    }

    /**
     * {@inheritdoc}
     * @throws \RuntimeException
     */
    public function load($resource, $type = null)
    {
        if (in_array($resource, $this->loadedResources)) {
            throw new \RuntimeException(sprintf('Resource "%s" was already loaded by the "%s" loader.',
                $resource, self::NAME
            ));
        }

        $routes = new RouteCollection();
        $baseReference = $this->templateNameParser->parse($resource);
        $loaderResource = $this->createLoaderResource($baseReference);
        $routes->addResource($loaderResource);

        foreach ($loaderResource as $fileInfo) {
            $uri = $this->getUri($fileInfo['pathname']);
            $name = $this->getRouteName($uri);

            if (null === $route = $routes->get($name)) {
                $i18nTemplate = $this->createI18nTemplate(
                    $baseReference->get('bundle'),
                    ltrim(dirname($fileInfo['pathname']), '/'),
                    basename($fileInfo['pathname']),
                    $baseReference->get('format'),
                    $baseReference->get('engine')
                );

                $routes->add($name, new Route($uri, [
                    self::ROUTE_PARAM => $i18nTemplate,
                ]));
            } else {
                /** @var I18nTemplateReference $i18nTemplate */
                $i18nTemplate = $route->getDefault(self::ROUTE_PARAM);
            }

            $i18nTemplate->addLocale(empty($fileInfo['locale']) ? '' : $fileInfo['locale']);
        }

        $this->loadedResources[] = $resource;

        return $routes;
    }

    /**
     * {@inheritdoc}
     */
    public function supports($resource, $type = null)
    {
        return $type === self::NAME;
    }

    /**
     * @param TemplateReference $reference
     * @return RegexFileStructureResource
     */
    protected function createLoaderResource(TemplateReference $reference)
    {
        $dir = $this->fileLocator->locate(dirname($reference->getPath()));

        return new RegexFileStructureResource($dir, sprintf(
            '#^%s(?<pathname>/.+?)(?:\.(?<locale>%s))?\.%s\.%s$#i',
            preg_quote($dir),
            implode('|', array_map('preg_quote', $this->locales)),
            preg_quote($reference->get('format')),
            preg_quote($reference->get('engine'))
        ));
    }

    /**
     * @param string|null $bundle
     * @param string|null $controller
     * @param string|null $name
     * @param string|null $format
     * @param string|null $engine
     * @return I18nTemplateReference
     */
    protected function createI18nTemplate(
        $bundle = null,
        $controller = null,
        $name = null,
        $format = null,
        $engine = null
    ) {
        return new I18nTemplateReference(
            new TemplateExportableReference($bundle, $controller, $name, $format, $engine)
        );
    }

    /**
     * @param $string
     * @return string
     */
    protected function getUri($string)
    {
        return ltrim(preg_replace('#/index$#i', '', $string), '/');
    }

    /**
     * @param string $uri
     * @param string $delimiter
     * @param string $rootName
     * @return string
     */
    protected function getRouteName($uri, $delimiter = '_', $rootName = 'index')
    {
        if ($uri === '') {
            return $rootName;
        }

        $routeName = preg_replace('/[\W]+/', $delimiter, $uri);

        return trim($routeName, $delimiter);
    }
}
