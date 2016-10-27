<?php

namespace Ruwork\RoutingBundle\Routing;

use Ruwork\RoutingBundle\Config\RegexFileStructureResource;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class TemplateLoader extends Loader
{
    /**
     * @var FileLocatorInterface|null
     */
    private $fileLocator;

    /**
     * @param FileLocatorInterface|null $fileLocator
     */
    public function __construct(FileLocatorInterface $fileLocator = null)
    {
        $this->fileLocator = $fileLocator;
    }

    /**
     * {@inheritdoc}
     */
    public function load($resource, $type = null)
    {
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

        return $routes;
    }

    /**
     * {@inheritdoc}
     */
    public function supports($resource, $type = null)
    {
        return $type === 'ruwork_template';
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
