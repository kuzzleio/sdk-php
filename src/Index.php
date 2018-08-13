<?php

namespace Kuzzle;

use InvalidArgumentException;

/**
 * Class Index
 * @package kuzzleio/kuzzle-sdk
 */
class Index
{
    /**
    * @var Kuzzle linked Kuzzle instance
    */
    protected $kuzzle;

    /**
     * Index controller constructor.
     *
     * @param Kuzzle $kuzzle Kuzzle server instance
     * @return Index
     */
    public function __construct($kuzzle)
    {
        $this->kuzzle = $kuzzle;
        return $this;
    }

    /**
     * Create an index
     *
     * @param string $index
     * @param array $options
     *
     * @return mixed
     *
     * @throws InvalidArgumentException
     */
    public function create($index, array $options = [])
    {
        if (empty($index)) {
            throw new InvalidArgumentException('Kuzzle\Index::create: Unable to create index: no index specified');
        }

        $options['httpParams'] = [
            ':index' => $index
        ];

        $response = $this->kuzzle->query(
            $this->kuzzle->buildQueryArgs('index', 'create'),
            [
                'index' => $index,
                'body' => ['index' => $index]
            ],
            $options
        );

        return $response['result'];
    }

    /**
     * Check if an index exist
     *
     * @param string $index
     * @param array $options
     *
     * @return boolean
     *
     * @throws InvalidArgumentException
     */
    public function exists($index, array $options = [])
    {
        if (empty($index)) {
            throw new InvalidArgumentException('Kuzzle\Index::exists: Unable to check if index exists: no index specified');
        }

        $options['httpParams'] = [
            ':index' => $index
        ];

        $response = $this->kuzzle->query(
            $this->kuzzle->buildQueryArgs('index', 'exists'),
            [
                'index' => $index
            ],
            $options
        );

        return $response['result'];
    }

    /**
     * Delete an index
     *
     * @param string $index
     * @param array $options
     *
     * @return mixed
     *
     * @throws InvalidArgumentException
     */
    public function deleteIndex($index, array $options = [])
    {
        if (empty($index)) {
            throw new InvalidArgumentException('Kuzzle\Index::delete: Unable to delete index: no index specified');
        }

        $options['httpParams'] = [
            ':index' => $index
        ];

        $response = $this->kuzzle->query(
            $this->kuzzle->buildQueryArgs('index', 'delete'),
            [
                'index' => $index
            ],
            $options
        );

        return $response['result'];
    }

    /**
     * Delete multiple index
     *
     * @param array $indexes
     * @param array $options
     *
     * @return mixed
     *
     * @throws InvalidArgumentException
     */
    public function mDelete(array $indexes, array $options = [])
    {
        if (!is_array($indexes) || empty($indexes)) {
            throw new InvalidArgumentException('Kuzzle\Index::mDelete: Unable to delete indexes: no indexes specified');
        }

        $response = $this->kuzzle->query(
            $this->kuzzle->buildQueryArgs('index', 'mDelete'),
            [
              'body' => [
                  'indexes' => json_encode($indexes)
              ]
            ],
            $options
        );

        return $response['result'];
    }


    /**
     * Retrieves the list of indexes stored in Kuzzle.
     *
     * @param array $options Optional parameters
     * @return array of index names
     */
    public function listIndexes(array $options = [])
    {
        $response = $this->kuzzle->query(
            $this->kuzzle->buildQueryArgs('index', 'list'),
            [],
            $options
        );

        return $response['result']['indexes'];
    }

    /**
     * Given an index, the refresh action forces a refresh, on it,
     * making the documents visible to search immediately.
     *
     * @param string $index The index to refresh. If not set, defaults to Kuzzle->defaultIndex.
     * @param array $options Optional parameters
     *
     * @return array structure matching the response from Elasticsearch
     *
     * @throws InvalidArgumentException
     */
    public function refresh($index, array $options = [])
    {
        if (empty($index)) {
            throw new InvalidArgumentException('Kuzzle\Index::refresh: Unable to refresh index: no index specified');
        }

        $options['httpParams'] = [
            ':index' => $index
        ];

        $response = $this->kuzzle->query(
            $this->kuzzle->buildQueryArgs('index', 'refresh', $index),
            [
                'index' => $index
            ],
            $options
        );

        return $response['result'];
    }

    /**
     * The refreshInternal action forces a refresh, on the
     * internal index, making the documents available to search immediately.
     *
     * @param array $options Optional parameters
     * @return array structure matching the response from Elasticsearch
     */
    public function refreshInternal(array $options = [])
    {
        $response = $this->kuzzle->query(
            $this->kuzzle->buildQueryArgs('index', 'refreshInternal'),
            [],
            $options
        );

        return $response['result'];
    }

    /**
     * Returns de current autoRefresh status for the given index
     *
     * @param string $index The index to get the status from.
     * @param array $options Optional parameters
     *
     * @return boolean
     *
     * @throws InvalidArgumentException
     */
    public function getAutoRefresh($index, array $options = [])
    {
        if (empty($index)) {
            throw new InvalidArgumentException('Kuzzle\Index::getAutoRefresh: Unable to get auto refresh on index: no index specified');
        }

        $options['httpParams'] = [
            ':index' => $index
        ];

        $response = $this->kuzzle->query(
            $this->kuzzle->buildQueryArgs('index', 'getAutoRefresh', $index),
            [
                'index' => $index
            ],
            $options
        );

        return $response['result'];
    }

    /**
     * The autoRefresh flag, when set to true,
     * will make Kuzzle perform a refresh request immediately after each write request,
     * forcing the documents to be immediately visible to search
     *
     * @param string $index Optional The index to set the autoRefresh for. If not set, defaults to Kuzzle->defaultIndex
     * @param bool $autoRefresh The value to set for the autoRefresh setting.
     * @param array $options Optional parameters
     *
     * @return boolean
     *
     * @throws InvalidArgumentException
     */
    public function setAutoRefresh($index, $autoRefresh = false, array $options = [])
    {
        if (empty($index) || is_null($autoRefresh)) {
            throw new InvalidArgumentException('Kuzzle\Index::setAutoRefresh: Unable to set auto refresh on index: no index specified or invalid value of autoRefresh');
        }

        $options['httpParams'] = [
            ':index' => $index
        ];

        $response = $this->kuzzle->query(
            $this->kuzzle->buildQueryArgs('index', 'setAutoRefresh', $index),
            [
                'index' => $index,
                'body' => [
                    'autoRefresh' => $autoRefresh
                ]
            ],
            $options
        );

        return $response['result'];
    }
}
