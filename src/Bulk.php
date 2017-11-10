<?php

namespace Kuzzle;

/**
 * Class Bulk
 * @package kuzzleio/kuzzle-sdk
 */
class Bulk
{
    /**
    * @var Kuzzle linked Kuzzle instance
    */
    protected $kuzzle;

    /**
     * Bulk controller constructor.
     *
     * @param Kuzzle $kuzzle Kuzzle server instance
     * @return Bulk
     */
    public function __construct($kuzzle)
    {
        $this->kuzzle = $kuzzle;
        return $this;
    }

    /**
     * Save a list of documents in a specific collection
     * using the Elasticsearch Bulk API
     *
     * @param array $data Array containing a list of JSON objects working in pairs.
     * See https://docs-v2.kuzzle.io/sdk-reference/bulk/import
     * @param array $options Optional parameter
     * @return mixed
     */
    public function import(array $data, array $options = [])
    {
        $response = $this->kuzzle->query(
            $this->kuzzle->buildQueryArgs('bulk', 'import'),
            [
                'body' => [
                    'bulkData' => json_encode($data)
                ]

            ],
            $options
        );

        return $response['result'];
    }
}
