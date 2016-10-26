<?php

namespace Ruwork\RoutingBundle\Config;

use IteratorAggregate, Serializable;
use RecursiveDirectoryIterator, FilesystemIterator, RecursiveIteratorIterator, RegexIterator;
use Symfony\Component\Config\Resource\SelfCheckingResourceInterface;

class FileStructureResource implements SelfCheckingResourceInterface, IteratorAggregate, Serializable
{
    /**
     * @var string
     */
    private $resource;

    /**
     * @var string
     */
    private $pattern;

    /**
     * @var string|null
     */
    private $hash;

    /**
     * @param string $resource
     * @param string $pattern
     */
    public function __construct($resource, $pattern)
    {
        $this->resource = $resource;
        $this->pattern = $pattern;
    }

    /**
     * @inheritdoc
     */
    public function __toString()
    {
        return sha1(serialize([
            get_class($this),
            $this->resource,
            $this->pattern,
        ]));
    }

    /**
     * @inheritdoc
     */
    public function isFresh($timestamp)
    {
        return $this->hash === $this->generateHash();
    }

    /**
     * @inheritdoc
     */
    public function getIterator()
    {
        $recursiveFilesIterator = new RecursiveDirectoryIterator($this->resource,
            FilesystemIterator::CURRENT_AS_PATHNAME | FilesystemIterator::SKIP_DOTS);
        $filesIterator = new RecursiveIteratorIterator($recursiveFilesIterator);

        return new RegexIterator($filesIterator, $this->pattern, RegexIterator::GET_MATCH);
    }

    /**
     * @inheritdoc
     */
    public function serialize()
    {
        return serialize([
            $this->resource,
            $this->pattern,
            $this->generateHash(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function unserialize($serialized)
    {
        list(
            $this->resource,
            $this->pattern,
            $this->hash
            ) = unserialize($serialized);
    }

    /**
     * @return string
     */
    private function generateHash()
    {
        return sha1(serialize(iterator_to_array($this->getIterator())));
    }
}
