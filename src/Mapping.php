<?php

namespace Kuzzle;

use ErrorException;

/**
 * Class Mapping
 * @package kuzzleio/kuzzle-sdk
 */
class Mapping
{
    /**
     * @var Collection related collection
     */
    protected $collection;

    /**
     * @var array Easy-to-understand list of mappings per field
     */
    protected $mapping = [];

    /**
     * @var array Common headers for all sent documents.
     */
    protected $headers = [];

    /**
     * Mapping constructor.
     *
     * @param Collection $kuzzleDataCollection An instantiated Kuzzle\Collection object
     * @param array $mapping Optional mapping
     */
    public function __construct(Collection $kuzzleDataCollection, array $mapping = [])
    {
        $this->collection = $kuzzleDataCollection;
        $this->mapping = $mapping;

        return $this;
    }

    /**
     * Applies the new mapping to the data collection.
     *
     * @param array $options Optional parameters
     * @return Mapping
     */
    public function apply(array $options = [])
    {
        $data = [
            'body' => [
                'properties' => $this->mapping
            ]
        ];

        $this->collection->getKuzzle()->query(
            $this->collection->buildQueryArgs('collection', 'updateMapping'),
            $this->collection->getKuzzle()->addHeaders($data, $this->headers),
            $options
        );

        return $this->refresh($options);
    }

    /**
     * Instantiates a new KuzzleDataMapping object with an up-to-date content.
     *
     * @param array $options Optional parameters
     * @return Mapping
     *
     * @throws ErrorException
     */
    public function refresh(array $options = [])
    {
        $response = $this->collection->getKuzzle()->query(
            $this->collection->buildQueryArgs('collection', 'getMapping'),
            $this->collection->getKuzzle()->addHeaders([], $this->headers),
            $options
        );

        if (array_key_exists($this->collection->getIndexName(), $response['result'])) {
            $indexMappings = $response['result'][$this->collection->getIndexName()]['mappings'];

            if (array_key_exists($this->collection->getCollectionName(), $indexMappings)) {
                $this->mapping = $indexMappings[$this->collection->getCollectionName()]['properties'];
            } else {
                throw new ErrorException('No mapping found for collection ' . $this->collection->getCollectionName());
            }
        } else {
            throw new ErrorException('No mapping found for index ' . $this->collection->getIndexName());
        }

        return $this;
    }

    /**
     * Adds or updates a field mapping.
     *
     * @param string $field Name of the field from which the mapping is to be added or updated
     * @param array $mapping Mapping for this field, following the Elasticsearch Mapping format
     * @return Mapping
     */
    public function set($field, array $mapping)
    {
        $this->mapping[$field] = $mapping;

        return $this;
    }

    /**
     * This is a helper function returning itself, allowing to easily chain calls.
     *
     * @param array $headers New content
     * @param bool $replace true: replace the current content with the provided data, false: merge it
     * @return Mapping
     */
    public function setHeaders(array $headers, $replace = false)
    {
        if ($replace) {
            $this->headers = $headers;
        } else {
            foreach ($headers as $key => $value) {
                $this->headers[$key] = $value;
            }
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @return Collection
     */
    public function getCollection()
    {
        return $this->collection;
    }

    /**
     * @return array
     */
    public function getMapping()
    {
        return $this->mapping;
    }
}
