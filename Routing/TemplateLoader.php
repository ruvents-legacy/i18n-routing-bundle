<?php

namespace Ruwork\RoutingBundle\Routing;

use Ruwork\RoutingBundle\Config\RegexFileStructureResource;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class TemplateLoader extends Loader
{
    const NAME = 'ruwork_template';

    /**
     * @var FileLocatorInterface|null
     */
    private $fileLocator;

    /**
     * @var string[]
     */
    private $loadedResources = [];

    /**
     * @param FileLocatorInterface|null $fileLocator
     */
    public function __construct(FileLocatorInterface $fileLocator = null)
    {
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

        $dir = $this->locateDirectory($resource);

        $structureResource = new RegexFileStructureResource($dir, sprintf(
            '/^%s(?<file>(?<uri>\/.+?)(?:|\.(?<locale>ru|en))\.page\.html\.twig)$/',
            preg_quote($dir, '/')
        ));

        $routes = new RouteCollection();
        $routes->addResource($structureResource);

        foreach ($structureResource as $fileInfo) {
            $uri = $this->getUri($fileInfo['uri']);

            $routes->add(
                $this->getRouteName($uri),
                new Route($uri, [
                    '_controller' => 'AppBundle:App:templateRouting',
                    'template' => $resource.$fileInfo['file'],
                ])
            );
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
     * @param string $resource
     * @return string
     * @throws \RuntimeException
     */
    private function locateDirectory($resource)
    {
        $dir = $this->fileLocator === null
            ? $resource
            : $this->fileLocator->locate($resource);

        if (!is_dir($dir)) {
            throw new \RuntimeException(sprintf('"%s" is not a directory.', $resource));
        }

        return rtrim(realpath($dir), '/');
    }

    /**
     * @param $string
     * @return string
     */
    private function getUri($string)
    {
        if ($string === '/index') {
            return '/';
        }

        return preg_replace('/\/index$/i', '', $string);
    }

    /**
     * @param string $uri
     * @param string $delimiter
     * @param string $rootName
     * @return string
     */
    private function getRouteName($uri, $delimiter = '_', $rootName = 'root')
    {
        if ($uri === '/') {
            return $rootName;
        }

        $routeName = preg_replace('/[\W]+/', $delimiter, $uri);

        return trim($routeName, $delimiter);
    }
}
