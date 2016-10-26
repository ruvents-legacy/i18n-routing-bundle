<?php

namespace Ruwork\RoutingBundle\Routing;

use Ruwork\RoutingBundle\Config\FileStructureResource;
use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class TemplateLoader extends Loader
{
    /**
     * @var KernelInterface|null
     */
    private $kernel;

    /**
     * @param KernelInterface|null $kernel
     */
    public function __construct(KernelInterface $kernel = null)
    {
        $this->kernel = $kernel;
    }

    /**
     * {@inheritdoc}
     */
    public function load($resource, $type = null)
    {
        $dir = $this->locateDirectory($resource);

        $structureResource = new FileStructureResource($dir, sprintf(
            '/^%s(?<file>(?<uri>\/.+?)(?:|\.(?<locale>ru|en))\.page\.html\.twig)$/',
            preg_quote($dir, '/')
        ));

        $routes = new RouteCollection();
        $routes->addResource($structureResource);

        foreach ($structureResource as $fileInfo) {
            $uri = preg_replace('/\/index$/', '', $fileInfo['uri']);
            $uri = $uri ?: '/';

            $routes->add(
                $this->generateRouteName($uri),
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
     * @param string $dir
     * @return string
     */
    private function locateDirectory($dir)
    {
        $dir = $this->kernel === null
            ? $dir
            : $this->kernel->locateResource($dir);

        return rtrim(realpath($dir), '/');
    }

    /**
     * @param string $uri
     * @param string $delimiter
     * @param string $rootName
     * @return string
     */
    private function generateRouteName($uri, $delimiter = '_', $rootName = 'root')
    {
        return $uri === '/'
            ? $rootName
            : trim(preg_replace('/[\W]+/', $delimiter, $uri), $delimiter);
    }
}
