<?php

namespace Ruwork\RoutingBundle\Routing;

use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Bundle\FrameworkBundle\Templating\TemplateNameParser;
use Symfony\Bundle\FrameworkBundle\Templating\TemplateReference;
use Ruwork\RoutingBundle\Config\Resource\RegexFileStructureResource;

class TemplateLoader extends Loader
{
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
     *
     * @throws \RuntimeException
     */
    public function load($resource, $type = null)
    {
        if (in_array($resource, $this->loadedResources)) {
            throw new \RuntimeException(sprintf('Resource "%s" was already loaded by the "%s" loader.',
                $resource, get_class($this)
            ));
        }

        $routes = new RouteCollection();
        $baseReference = $this->templateNameParser->parse($resource);
        $loaderResource = $this->createStructureResource($baseReference);
        $routes->addResource($loaderResource);

        foreach ($loaderResource as $matches) {
            $uri = $this->getUri($matches);
            $name = $this->getRouteName($matches);

            if (null !== $routes->get($name)) {
                continue;
            }

            $templateReference = new TemplateReference(
                $baseReference->get('bundle'),
                $matches['path'],
                $matches['name'],
                $baseReference->get('format'),
                $baseReference->get('engine')
            );

            $routes->add($name, new Route($uri, [
                'template' => $templateReference->__toString(),
            ]));
        }

        $this->loadedResources[] = $resource;

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
     * @param TemplateReference $reference
     *
     * @return RegexFileStructureResource
     */
    protected function createStructureResource(TemplateReference $reference)
    {
        $dir = $this->fileLocator->locate(dirname($reference->getPath()));

        return new RegexFileStructureResource($dir, sprintf(
            '#^%s/?(?<'.'path>[\w-/]*?)/(?<'.'name>[\w-]+)\.%s\.%s$#',
            preg_quote($dir),
            preg_quote($reference->get('format')),
            preg_quote($reference->get('engine'))
        ));
    }

    /**
     * @param array $matches
     *
     * @return string
     */
    protected function getUri(array $matches)
    {
        return $matches['path'].($matches['name'] === 'index' ? '' : '/'.$matches['name']);
    }

    /**
     * @param array  $matches
     * @param string $slashReplacement
     * @param string $rootName
     *
     * @return string
     */
    protected function getRouteName(array $matches, $slashReplacement = '_', $rootName = 'index')
    {
        $path = str_replace('/', $slashReplacement, $matches['path']);
        $name = $matches['name'] === 'index' ? '' : $matches['name'];

        if ($path === '') {
            return $name === '' ? $rootName : $name;
        }

        return $path.($name === '' ? '' : $slashReplacement).$name;
    }
}
