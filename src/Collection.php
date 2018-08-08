<?php

namespace Kuzzle;

use Kuzzle\Util\SearchResult;
use InvalidArgumentException;

/**
 * Class Collection
 * @package kuzzleio/kuzzle-sdk
 */
class Collection
{
    /**
     * @var Kuzzle linked kuzzle instance
     */
    protected $kuzzle;

    /**
     * Collection controller constructor.
     *
     * @param Kuzzle $kuzzle Kuzzle object
     * @return Collection
     */
    public function __construct(Kuzzle $kuzzle)
    {
        $this->kuzzle = $kuzzle;

        return $this;
    }

    /**
     * Create a new empty data collection. An optional mapping
     * object can be provided
     *
     * @param string $index Index name
     * @param string $collection Collection name
     * @param array $mapping Optional collection mapping description
     * @param array $options Optional parameters
     *
     * @return boolean
     *
     * @throws InvalidArgumentException
     *
     */
    public function create($index, $collection, array $mapping = [], array $options = [])
    {
        if (empty($index) || empty($collection)) {
            throw new InvalidArgumentException('Kuzzle\Collection::create: index and collection name required');
        }

        $options['httpParams'] = [
            ':index' => $index,
            ':collection' => $collection
        ];

        $response = $this->kuzzle->query(
            $this->kuzzle->buildQueryArgs('collection', 'create'),
            [   'index' => $index,
                'collection' => $collection,
                'body' => json_encode($mapping)
            ],
            $options
        );

        return $response['result']['acknowledged'];
    }

    /**
     * Checks if a collection exists in Kuzzle.
     *
     * @param string $index Index name
     * @param string $collection Collection name
     * @param array $options Optional parameters
     *
     * @return boolean
     *
     * @throws InvalidArgumentException
     *
     */
    public function exists($index, $collection, array $options = [])
    {
        if (empty($index) || empty($collection)) {
            throw new InvalidArgumentException('Kuzzle\Collection::exists: index and collection name required');
        }

        $options['httpParams'] = [
            ':index' => $index,
            ':collection' => $collection
        ];

        $response = $this->kuzzle->query(
            $this->kuzzle->buildQueryArgs('collection', 'exists'),
            [
                'index' => $index,
                'collection' => $collection
            ],
            $options
        );

        return $response['result'];
    }

    /**
     * Returns the complete list of realtime and stored data collections in
     * requested index sorted by name in alphanumerical order.
     *
     * @param string $index Index name
     * @param array $options Optional parameters
     *
     * @return mixed
     *
     * @throws InvalidArgumentException
     */
    public function listCollections($index, array $options = [])
    {
        if (empty($index)) {
            throw new InvalidArgumentException('Kuzzle\Collection::listCollections: index name required');
        }

        $options['httpParams'] = [
            ':index' => $index,
        ];

        $response = $this->kuzzle->query(
            $this->kuzzle->buildQueryArgs('collection', 'list'),
            [
                'index' => $index
            ],
            $options
        );

        return $response['result']['collections'];
    }

    /**
     * Truncate the data collection,
     * removing all stored documents but keeping all associated mappings.
     *
     * @param string $index Index name
     * @param string $collection Collection name
     * @param array $options Optional parameters
     *
     * @return boolean
     *
     * @throws InvalidArgumentException
     */
    public function truncate($index, $collection, array $options = [])
    {
        if (empty($index) || empty($collection)) {
            throw new InvalidArgumentException('Kuzzle\Collection::truncate: index and collection name required');
        }

        $options['httpParams'] = [
            ':index' => $index,
            ':collection' => $collection
        ];

        $response = $this->kuzzle->query(
            $this->kuzzle->buildQueryArgs('collection', 'truncate'),
            [
                'index' => $index,
                'collection' => $collection
            ],
            $options
        );

        return $response['result']['acknowledged'];
    }

    /**
     * Retrieves the current specifications of this collection
     *
     * @param string $index Index name
     * @param string $collection Collection name
     * @param array $options Optional parameters
     *
     * @return mixed
     *
     * @throws InvalidArgumentException
     */
    public function getSpecifications($index, $collection, array $options = [])
    {
        if (empty($index) || empty($collection)) {
            throw new InvalidArgumentException('Kuzzle\Collection::getSpecifications: index and collection name required');
        }

        $options['httpParams'] = [
            ':index' => $index,
            ':collection' => $collection
        ];

        $response = $this->kuzzle->query(
            $this->kuzzle->buildQueryArgs('collection', 'getSpecifications'),
            [
                'index' => $index,
                'collection' => $collection
            ],
            $options
        );

        return $response['result'];
    }

    /**
     * Deletes the current specifications of this collection
     *
     * @param string $index Index name
     * @param string $collection Collection name
     * @param array $options Optional parameters
     *
     * @return boolean
     *
     * @throws InvalidArgumentException
     */
    public function deleteSpecifications($index, $collection, array $options = [])
    {
        if (empty($index) || empty($collection)) {
            throw new InvalidArgumentException('Kuzzle\Collection::deleteSpecifications: index and collection name required');
        }

        $options['httpParams'] = [
            ':index' => $index,
            ':collection' => $collection
        ];

        $response = $this->kuzzle->query(
            $this->kuzzle->buildQueryArgs('collection', 'deleteSpecifications'),
            [
                'index' => $index,
                'collection' => $collection
            ],
            $options
        );

        return $response['result'];
    }

    /**
     * Scrolls through specifications using the provided scrollId
     *
     * @param string $scrollId
     * @param array $options Optional parameters
     *
     * @return mixed
     *
     * @throws InvalidArgumentException
     */
    public function scrollSpecifications($scrollId, array $options = [])
    {
        if (empty($scrollId)) {
            throw new InvalidArgumentException('Kuzzle\Collection::scrollSpecifications: scrollId is required');
        }

        $options['httpParams'] = [
            ':scrollId' => $scrollId
        ];

        $response = $this->kuzzle->query(
            $this->kuzzle->buildQueryArgs('collection', 'scrollSpecifications'),
            [
                'scrollId' => $scrollId,
            ],
            $options
        );

        return $response['result'];
    }

    /**
     * Searches specifications across indexes/collections according to the provided filters
     *
     * @param array $filters Optional filters in ElasticSearch Query DSL format
     * @param array $options Optional parameters
     *
     * @return mixed
     */
    public function searchSpecifications(array $filters = [], array $options = [])
    {
        $response = $this->kuzzle->query(
            $this->kuzzle->buildQueryArgs('collection', 'searchSpecifications'),
            [
                'body' => [
                    'query' => json_encode($filters)
                ]
            ],
            $options
        );

        return $response['result'];
    }

    /**
     * Updates the current specifications of this collection
     *
     * @param string $index Index name
     * @param string $collection Collection name
     * @param array $specifications Specifications content
     * @param array $options Optional parameters
     *
     * @return mixed
     *
     * @throws InvalidArgumentException
     */
    public function updateSpecifications($index, $collection, array $specifications, array $options = [])
    {
        if (empty($index) || empty($collection) || !is_array($specifications)) {
            throw new InvalidArgumentException('Kuzzle\Collection::updateSpecifications: index name, collection name and specifications as an array are required');
        }

        $response = $this->kuzzle->query(
            $this->kuzzle->buildQueryArgs('collection', 'updateSpecifications'),
            [
                'body' => [
                    $index => [
                        $collection => json_encode($specifications)
                    ]
                ]
            ],
            $options
        );

        return $response['result'];
    }

    /**
     * Validates the provided specifications
     *
     * @param string $index Index name
     * @param string $collection Collection name
     * @param array $specifications Specifications content
     * @param array $options Optional parameters
     *
     * @return boolean
     *
     * @throws InvalidArgumentException
     */
    public function validateSpecifications($index, $collection, array $specifications, array $options = [])
    {
        if (empty($index) || empty($collection) || !is_array($specifications)) {
            throw new InvalidArgumentException('Kuzzle\Collection::validateSpecifications: index name, collection name and specifications as an array are required');
        }

        $response = $this->kuzzle->query(
            $this->kuzzle->buildQueryArgs('collection', 'validateSpecifications'),
            [
                'body' => [
                    $index => [
                        $collection => json_encode($specifications)
                    ]
                ]
            ],
            $options
        );

        return $response['result']['valid'];
    }

    /**
     * Retrieves the current mapping of this collection.
     *
     * @param string $index
     * @param string $collection
     * @param array $options Optional parameters
     *
     * @return mixed
     *
     * @throws InvalidArgumentException
     */
    public function getMapping($index, $collection, array $options = [])
    {
        if (empty($index) || empty($collection)) {
            throw new InvalidArgumentException('Kuzzle\Collection::getMapping: index and collection name required');
        }

        $options['httpParams'] = [
            ':index' => $index,
            ':collection' => $collection
        ];

        $response = $this->kuzzle->query(
            $this->kuzzle->buildQueryArgs('collection', 'getMapping'),
            [
                'index' => $index,
                'collection' => $collection
            ],
            $options
        );

        return $response['result'];
    }

    /**
     * Applies the new mapping to the data collection.
     *
     * @param string $index
     * @param string $collection
     * @param array $mapping new Mapping
     * @param array $options Optional parameters
     *
     * @return mixed
     *
     * @throws InvalidArgumentException
     */
    public function updateMapping($index, $collection, array $mapping, array $options = [])
    {
        if (empty($index) || empty($collection) || !is_array($mapping)) {
            throw new InvalidArgumentException('Kuzzle\Collection::updateMapping: index name, collection name and mapping as an array are required');
        }

        $options['httpParams'] = [
            ':index' => $index,
            ':collection' => $collection
        ];

        $response = $this->kuzzle->query(
            $this->kuzzle->buildQueryArgs('collection', 'updateMapping'),
            [
                'index' => $index,
                'collection' => $collection,
                'body' => [
                    'properties' => json_encode($mapping)
                ]
            ],
            $options
        );

        return $response['result'];
    }
}
