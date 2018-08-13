<?php

namespace Kuzzle;

use InvalidArgumentException;
use Kuzzle\Util\SearchResult;

/**
 * Class Document
 * @package kuzzleio/kuzzle-sdk
 */
class Document
{
    /**
    * @var Kuzzle linked Kuzzle instance
    */
    protected $kuzzle;

    /**
     * Document controller constructor.
     *
     * @param Kuzzle $kuzzle Kuzzle server instance
     * @return Document
     */
    public function __construct($kuzzle)
    {
        $this->kuzzle = $kuzzle;
        return $this;
    }

    /**
     * Create a document in Kuzzle.
     *
     * This function will create it in Kuzzle. The id can be passed as argument
     * or user can let Kuzzle generate one for him.
     *
     * @param string $index Index name
     * @param string $collection Collection name
     * @param string $_id document unique ID
     * @param array $body Document body
     * @param array $options Optional parameters
     *
     * @return mixed
     *
     * @throws InvalidArgumentException
     */
    public function create($index, $collection, $_id, array $body, array $options = [])
    {
        if (empty($index) || empty($collection)|| empty($_id) || empty($body)) {
            throw new InvalidArgumentException('Kuzzle\Document::create: cannot create a document without an index, a collection, a document ID and a document body');
        }

        $options['httpParams'] = [
            ':index' => $index,
            ':collection' => $collection,
            ':_id' => $_id
        ];

        $response = $this->kuzzle->query(
            $this->kuzzle->buildQueryArgs('document', 'create'),
            [
              'index' => $index,
              'collection' => $collection,
              '_id' => $_id,
                'body' => json_encode($body)
            ],
            $options
        );

        return $response['result'];
    }

    /**
     * Deletes this document in Kuzzle
     *
     * @param string $index Index name
     * @param string $collection Collection name
     * @param string $_id document unique ID
     * @param array $options Optional parameters
     *
     * @return mixed
     *
     * @throws InvalidArgumentException
     */
    public function delete($index, $collection, $_id, array $options = [])
    {
        if (empty($index) || empty($collection) || empty($_id)) {
            throw new InvalidArgumentException('Kuzzle\Document::delete: cannot delete a document without an index, a collection and a document ID');
        }

        $options['httpParams'] = [
            ':index' => $index,
            ':collection' => $collection,
            ':_id' => $_id,
        ];

        $response = $this->kuzzle->query(
            $this->kuzzle->buildQueryArgs('document', 'delete'),
            [
                'index' => $index,
                'collection' => $collection,
                '_id' => $_id,
            ],
            $options
        );

        return $response['result'];
    }

    /**
     * Deletes all matching document in Kuzzle
     *
     * @param string $index Index name
     * @param string $collection Collection name
     * @param string $_id document unique ID
     * @param array $options Optional parameters
     *
     * @return mixed
     *
     * @throws InvalidArgumentException
     */
    public function deleteByQuery($index, $collection, array $query, array $options = [])
    {
        if (empty($index) || empty($collection) || empty($query)) {
            throw new InvalidArgumentException('Kuzzle\Document::deleteByQuery: cannot delete a document by query without an index, a collection and a query');
        }

        $options['httpParams'] = [
            ':index' => $index,
            ':collection' => $collection,
        ];

        $response = $this->kuzzle->query(
            $this->kuzzle->buildQueryArgs('document', 'deleteByQuery'),
            [
                'index' => $index,
                'collection' => $collection,
                'body' => ['query' => json_encode($query)],
            ],
            $options
        );

        return $response['result']['hits'];
    }

    /**
     * Checks if this document exists in Kuzzle.
     *
     * @param string $index Index name
     * @param string $collection Collection name
     * @param string $_id document unique ID
     * @param array $options
     *
     * @return boolean
     *
     * @throws InvalidArgumentException
     */
    public function exists($index, $collection, $_id, array $options = [])
    {
        if (empty($index) || empty($collection)|| empty($_id)) {
            throw new InvalidArgumentException('Kuzzle\Document::exists: cannot check if a document exists without an index, a collection and a document ID');
        }

        $options['httpParams'] = [
            ':index' => $index,
            ':collection' => $collection,
            ':_id' => $_id,
        ];

        $response = $this->kuzzle->query(
            $this->kuzzle->buildQueryArgs('document', 'exists'),
            [
                'index' => $index,
                'collection' => $collection,
                '_id' => $_id,
            ],
            $options
        );

        return $response['result'];
    }

    /**
     * Retrieves a single stored document using its unique document ID.
     *
     * @param string $index Index name
     * @param string $collection Collection name
     * @param string $_id document unique ID
     * @param array $options Optional parameters
     *
     * @return mixed
     *
     * @throws InvalidArgumentException
     */
    public function get($index, $collection, $_id, array $options = [])
    {
        if (empty($index) || empty($collection)|| empty($_id)) {
            throw new InvalidArgumentException('Kuzzle\Document::get: cannot retrieve a document without an index, a collection and a document ID');
        }

        $options['httpParams'] = [
            ':index' => $index,
            ':collection' => $collection,
            ':_id' => $_id,
        ];

        $response = $this->kuzzle->query(
            $this->kuzzle->buildQueryArgs('document', 'get'),
            [
                'index' => $index,
                'collection' => $collection,
                '_id' => $_id,
            ],
            $options
        );

        return $response['result'];
    }

    /**
     * Returns the number of documents matching the provided set of filters.
     *
     * @param string $index Index name
     * @param string $collection Collection name
     * @param array $filters Filters in ElasticSearch Query DSL format
     * @param array $options Optional parameters
     *
     * @return integer the matched documents count
     *
     * @throws InvalidArgumentException
     */
    public function count($index, $collection, array $filters, array $options = [])
    {
        if (empty($index) || !$collection) {
            throw new InvalidArgumentException('Kuzzle\Document::count: cannot count documents without an index and a collection');
        }

        $options['httpParams'] = [
            ':index' => $index,
            ':collection' => $collection
        ];

        $response = $this->kuzzle->query(
            $this->kuzzle->buildQueryArgs('document', 'count'),
            [
              'index' => $index,
              'collection' => $collection,
              'body' => [
                  'filters' => json_encode($filters)
              ]
            ],
            $options
        );

        return $response['result']['count'];
    }

    /**
     * Replace an existing document with a new one.
     *
     * @param string $index Index name
     * @param string $collection Collection name
     * @param string $_id document unique ID
     * @param array $body new document body
     * @param array $options Optional parameters
     *
     * @return mixed
     *
     * @throws InvalidArgumentException
     */
    public function replace($index, $collection, $_id, array $body, array $options = [])
    {
        if (empty($index) || empty($collection)|| empty($_id) || empty($body)) {
            throw new InvalidArgumentException('Kuzzle\Document::replace: cannot replace a document without an index, a collection, a document ID and a document body');
        }

        $options['httpParams'] = [
            ':index' => $index,
            ':collection' => $collection,
            ':_id' => $_id
        ];

        $response = $this->kuzzle->query(
            $this->kuzzle->buildQueryArgs('document', 'replace'),
            [
                'index' => $index,
                'collection' => $collection,
                '_id' => $_id,
                'body' => json_encode($body)
            ],
            $options
        );

        return $response['result'];
    }

    /**
     * Update parts of a document, by replacing some fields or adding new ones.
     * Note that you cannot remove fields this way: missing fields will simply be left unchanged.
     *
     * @param string $index Index name
     * @param string $collection Collection name
     * @param string $_id document unique ID
     * @param array $body new document body
     * @param array $options Optional parameters
     *
     * @return mixed
     *
     * @throws InvalidArgumentException
     */
    public function update($index, $collection, $_id, array $body, array $options = [])
    {
        if (empty($index) || empty($collection)|| empty($_id) || empty($body)) {
            throw new InvalidArgumentException('Kuzzle\Document::update: cannot update a document without an index, a collection, a document ID and a document body');
        }

        $options['httpParams'] = [
            ':index' => $index,
            ':collection' => $collection,
            ':_id' => $_id
        ];

        $response = $this->kuzzle->query(
            $this->kuzzle->buildQueryArgs('document', 'update'),
            [
                'index' => $index,
                'collection' => $collection,
                '_id' => $_id,
                'body' => json_encode($body)
            ],
            $options
        );

        return $response['result'];
    }

    /**
     * Validates data against existing validation rules. If the document is valid,
     * the result.valid value is true, if not, it is false. If the document
     * is not valid, the result.errorMessages will contain detailed hints on what
     * is wrong with the document.
     *
     * This request does not store or publish the document.
     *
     * @param string $index Index name
     * @param string $collection Collection name
     * @param array $body new document body
     * @param array $options Optional parameters
     *
     * @return mixed
     *
     * @throws InvalidArgumentException
     */
    public function validate($index, $collection, array $body, array $options = [])
    {
        if (empty($index) || empty($collection) || empty($body)) {
            throw new InvalidArgumentException('Kuzzle\Document::validate: cannot update a document without an index, a collection and a document body');
        }

        $options['httpParams'] = [
            ':index' => $index,
            ':collection' => $collection,
        ];

        $response = $this->kuzzle->query(
            $this->kuzzle->buildQueryArgs('document', 'validate'),
            [
                'index' => $index,
                'collection' => $collection,
                'body' => json_encode($body)
            ],
            $options
        );

        return $response['result'];
    }

    /**
     * Executes an advanced search on the data collection.
     *
     * @param string $index Index name
     * @param string $collection Collection name
     * @param array $filters Filters in ElasticSearch Query DSL format
     * @param array $options Optional parameters
     *
     * @return SearchResult
     *
     * @throws InvalidArgumentException
     */
    public function search($index, $collection, array $filters, array $options = [])
    {
        if (empty($index) || empty($collection)) {
            throw new InvalidArgumentException('Kuzzle\Document::search: cannot search documents without an index and a collection');
        }

        $response = $this->kuzzle->query(
            $this->kuzzle->buildQueryArgs('document', 'search'),
            [
                'index' => $index,
                'collection' => $collection,
                'body' => json_encode($filters)
            ],
            $options
        );

        if (array_key_exists('_scroll_id', $response['result'])) {
            $options['scrollId'] = $response['result']['_scroll_id'];
        }

        return new SearchResult(
            $collection,
            $response['result']['total'],
            $response['result']['hits'],
            array_key_exists('aggregations', $response['result']) ? $response['result']['aggregations'] : [],
            $options,
            $filters,
            array_key_exists('previous', $options) ? $options['previous'] : null
        );
    }

    /**
     * Create the provided documents
     *
     * @param string $index Index name
     * @param string $collection Collection name
     * @param array $documents Array of documents to create
     * @param array $options Optional parameters
     *
     * @return mixed
     *
     * @throws InvalidArgumentException
     */
    public function mCreate($index, $collection, $documents, array $options = [])
    {
        if (empty($index) || empty($collection)|| empty($documents)) {
            throw new InvalidArgumentException('Kuzzle\Document::mCreate: index and collection parameters missing or documents parameter format is invalid (should be an array of documents)');
        }

        $options['httpParams'] = [
            ':index' => $index,
            ':collection' => $collection,
        ];

        $response = $this->kuzzle->query(
            $this->kuzzle->buildQueryArgs('document', 'mCreate'),
            [
                'index' => $index,
                'collection' => $collection,
                'body' => [
                    'documents' => json_encode($documents)
                ]
            ],
            $options
        );

        return $response['result'];
    }

    /**
     * Create or replace the provided documents
     *
     * @param string $index Index name
     * @param string $collection Collection name
     * @param array $documents Array of documents to create or replace
     * @param array $options Optional parameters
     *
     * @return mixed
     *
     * @throws InvalidArgumentException
     */
    public function mCreateOrReplace($index, $collection, $documents, array $options = [])
    {
        if (empty($index) || empty($collection)|| empty($documents)) {
            throw new InvalidArgumentException('Kuzzle\Document::mCreateOrReplace: index and collection parameters missing or documents parameter format is invalid (should be an array of documents)');
        }

        $options['httpParams'] = [
            ':index' => $index,
            ':collection' => $collection,
        ];

        $response = $this->kuzzle->query(
            $this->kuzzle->buildQueryArgs('document', 'mCreateOrReplace'),
            [
                'index' => $index,
                'collection' => $collection,
                'body' => [
                    'documents' => json_encode($documents)
                ]
            ],
            $options
        );

        return $response['result'];
    }

    /**
     * Delete specific documents according to given IDs
     *
     * @param string $index Index name
     * @param string $collection Collection name
     * @param array $documentIds IDs of the documents to delete
     * @param array $options Optional parameters
     *
     * @return mixed
     *
     * @throws InvalidArgumentException
     */
    public function mDelete($index, $collection, $documentIds, array $options = [])
    {
        if (empty($index) || empty($collection)|| empty($documentIds)) {
            throw new InvalidArgumentException('Kuzzle\Document::mDelete: index and collection parameters missing or documents parameter format is invalid (should be an array of document IDs)');
        }

        $options['httpParams'] = [
            ':index' => $index,
            ':collection' => $collection,
        ];

        $response = $this->kuzzle->query(
            $this->kuzzle->buildQueryArgs('document', 'mDelete'),
            [
                'index' => $index,
                'collection' => $collection,
                'body' => [
                    'ids' => json_encode($documentIds)
                ]
            ],
            $options
        );

        return $response['result'];
    }

    /**
     * Get specific documents according to given IDs
     *
     * @param string $index Index name
     * @param string $collection Collection name
     * @param array $documentIds IDs of the documents to delete
     * @param array $options Optional parameters
     *
     * @return mixed
     *
     * @throws InvalidArgumentException
     */
    public function mGet($index, $collection, $documentIds, array $options = [])
    {
        if (empty($index) || empty($collection)|| empty($documentIds)) {
            throw new InvalidArgumentException('Kuzzle\Document::mGet: index and collection parameters missing or documents parameter format is invalid (should be an array of document IDs)');
        }

        $options['httpParams'] = [
            ':index' => $index,
            ':collection' => $collection,
        ];

        $response = $this->kuzzle->query(
            $this->kuzzle->buildQueryArgs('document', 'mGet'),
            [
                'index' => $index,
                'collection' => $collection,
                'body' => [
                    'ids' => json_encode($documentIds)
                ]
            ],
            $options
        );

        return $response['result'];
    }

    /**
     * Replace the provided documents
     *
     * @param string $index Index name
     * @param string $collection Collection name
     * @param array $documents Array of documents to create or replace
     * @param array $options Optional parameters
     *
     * @return mixed
     *
     * @throws InvalidArgumentException
     */
    public function mReplace($index, $collection, $documents, array $options = [])
    {
        if (empty($index) || empty($collection)|| empty($documents)) {
            throw new InvalidArgumentException('Kuzzle\Document::mReplace: index and collection parameters missing or documents parameter format is invalid (should be an array of documents)');
        }

        $options['httpParams'] = [
            ':index' => $index,
            ':collection' => $collection,
        ];

        $response = $this->kuzzle->query(
            $this->kuzzle->buildQueryArgs('document', 'mReplace'),
            [
                'index' => $index,
                'collection' => $collection,
                'body' => [
                    'documents' => json_encode($documents)
                ]
            ],
            $options
        );

        return $response['result'];
    }

    /**
     * Update the provided documents
     *
     * @param string $index Index name
     * @param string $collection Collection name
     * @param array $documents Array of documents to create or replace
     * @param array $options Optional parameters
     *
     * @return mixed
     *
     * @throws InvalidArgumentException
     */
    public function mUpdate($index, $collection, $documents, array $options = [])
    {
        if (empty($index) || empty($collection)|| empty($documents)) {
            throw new InvalidArgumentException('Kuzzle\Document::mUpdate: index and collection parameters missing or documents parameter format is invalid (should be an array of documents)');
        }

        $options['httpParams'] = [
            ':index' => $index,
            ':collection' => $collection,
        ];

        $response = $this->kuzzle->query(
            $this->kuzzle->buildQueryArgs('document', 'mUpdate'),
            [
                'index' => $index,
                'collection' => $collection,
                'body' => [
                    'documents' => json_encode($documents)
                ]
            ],
            $options
        );

        return $response['result'];
    }
}
