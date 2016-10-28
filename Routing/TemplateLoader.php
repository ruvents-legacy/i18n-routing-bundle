<?php

namespace Ruwork\RoutingBundle\Routing;

use Ruwork\RoutingBundle\Config\Resource\RegexFileStructureResource;
use Ruwork\RoutingBundle\Templating\TemplateReference;
use Symfony\Bundle\FrameworkBundle\Templating\TemplateNameParser;
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

        $baseRef = $this->templateNameParser->parse($resource);
        $dir = $this->fileLocator->locate(dirname($baseRef->getPath()));

        $structureResource = new RegexFileStructureResource($dir, sprintf(
            '#^%s(/.+)(?:\.(?:%s))?\.%s\.%s$#i',
            preg_quote($dir),
            implode('|', array_map('preg_quote', ['ru', 'en'])),
            preg_quote($baseRef->get('format')),
            preg_quote($baseRef->get('engine'))
        ));

        $routes = new RouteCollection();
        $routes->addResource($structureResource);

        foreach ($structureResource as $fileInfo) {
            $template = new TemplateReference(
                $baseRef->get('bundle'),
                ltrim(dirname($fileInfo[1]), '/'),
                basename($fileInfo[1]),
                $baseRef->get('format'),
                $baseRef->get('engine')
            );

            $uri = $this->getUri($fileInfo[1]);
            $name = $this->getRouteName($uri);

            if ($routes->get($name) !== null) {
                continue;
            }

            $routes->add($name, new Route($uri, [
                'template' => $template,
            ]));
        }

        $this->loadedResources[] = $baseRef;

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
