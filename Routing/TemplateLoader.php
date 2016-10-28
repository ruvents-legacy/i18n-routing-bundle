<?php

namespace Ruwork\RoutingBundle\Routing;

use Ruwork\RoutingBundle\Config\Resource\RegexFileStructureResource;
use Ruwork\RoutingBundle\Templating\ExportableTemplateReference;
use Ruwork\RoutingBundle\Templating\I18nTemplateReference;
use Symfony\Bundle\FrameworkBundle\Templating\TemplateNameParser;
use Symfony\Bundle\FrameworkBundle\Templating\TemplateReference;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class TemplateLoader extends Loader
{
    const NAME = 'ruwork_template';

    /**
     * @var TemplateNameParser
     */
    private $templateNameParser;

    /**
     * @var FileLocatorInterface|null
     */
    private $fileLocator;

    /**
     * @var string[]
     */
    private $loadedResources = [];

    /**
     * @param TemplateNameParser   $templateNameParser
     * @param FileLocatorInterface $fileLocator
     */
    public function __construct(TemplateNameParser $templateNameParser, FileLocatorInterface $fileLocator)
    {
        $this->templateNameParser = $templateNameParser;
        $this->fileLocator = $fileLocator;
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

        $baseReference = $this->templateNameParser->parse($resource);
        $structureResource = $this->createStructureResource($baseReference);

        $routes = new RouteCollection();
        $routes->addResource($structureResource);

        foreach ($structureResource as $fileInfo) {
            $uri = $this->getUri($fileInfo[1]);
            $name = $this->getRouteName($uri);

            if (null === $route = $routes->get($name)) {
                $i18nReference = new I18nTemplateReference(
                    new ExportableTemplateReference(
                        $baseReference->get('bundle'),
                        ltrim(dirname($fileInfo[1]), '/'),
                        basename($fileInfo[1]),
                        $baseReference->get('format'),
                        $baseReference->get('engine')
                    )
                );

                $routes->add($name, new Route($uri, [
                    'reference' => $i18nReference,
                ]));
            } else {
                /** @var I18nTemplateReference $i18nReference */
                $i18nReference = $route->getDefault('reference');
            }

            $i18nReference->addLocale(empty($fileInfo[2]) ? '' : $fileInfo[2]);
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
    private function createStructureResource(TemplateReference $reference)
    {
        $dir = $this->fileLocator->locate(dirname($reference->getPath()));

        return new RegexFileStructureResource($dir, sprintf(
            '#^%s(/.+?)(?:\.(%s))?\.%s\.%s$#i',
            preg_quote($dir),
            implode('|', array_map('preg_quote', ['ru', 'en'])),
            preg_quote($reference->get('format')),
            preg_quote($reference->get('engine'))
        ));
    }

    /**
     * @param $string
     * @return string
     */
    private function getUri($string)
    {
        return ltrim(preg_replace('#/index$#i', '', $string), '/');
    }

    /**
     * @param string $uri
     * @param string $delimiter
     * @param string $rootName
     * @return string
     */
    private function getRouteName($uri, $delimiter = '_', $rootName = 'index')
    {
        if ($uri === '') {
            return $rootName;
        }

        $routeName = preg_replace('/[\W]+/', $delimiter, $uri);

        return trim($routeName, $delimiter);
    }
}
