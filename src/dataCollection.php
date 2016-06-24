<?php

namespace Kuzzle;

use Kuzzle\Util\AdvancedSearchResult;

/**
 * Class DataCollection
 * @package kuzzleio/kuzzle-sdk
 */
class DataCollection
{
    /**
     * @var Kuzzle linked kuzzle instance
     */
    protected $kuzzle;

    /**
     * @var string Name of the index containing the data collection
     */
    protected $index;

    /**
     * @var string The name of the data collection you want to manipulate
     */
    protected $collection;

    /**
     * @var array Headers for all sent documents.
     */
    protected $headers = [];

    /**
     * DataCollection constructor.
     *
     * @param Kuzzle $kuzzle Kuzzle object
     * @param string $index Name of the index containing the data collection
     * @param string $collection The name of the data collection you want to manipulate
     */
    public function __construct(Kuzzle $kuzzle, $index, $collection)
    {
        $this->kuzzle = $kuzzle;
        $this->index = $index;
        $this->collection = $collection;

        return $this;
    }

    /**
     * Executes an advanced search on the data collection.
     *
     * @param array $filters Filters in ElasticSearch Query DSL format
     * @param array $options Optional parameters
     * @return AdvancedSearchResult
     */
    public function advancedSearch(array $filters, array $options = [])
    {
        $data = [
            'body' => $filters
        ];

        $response = $this->kuzzle->query(
            $this->buildQueryArgs('read', 'search'),
            $this->kuzzle->addHeaders($data, $this->headers),
            $options
        );

        $documents = [];

        foreach($response['result']['hits'] as $documentInfo)
        {
            $content = $documentInfo['_source'];

            $document = new Document($this, $documentInfo['_id'], $content);

            $documents[] = $document;
        }

        return new AdvancedSearchResult($response['result']['total'], $documents);
    }

    /**
     * Returns the number of documents matching the provided set of filters.
     *
     * @param array $filters Filters in ElasticSearch Query DSL format
     * @param array $options Optional parameters
     *
     * @return integer the matched documents count
     */
    public function count(array $filters, array $options = [])
    {
        $data = [
            'body' => $filters
        ];

        $response = $this->kuzzle->query(
            $this->buildQueryArgs('read', 'count'),
            $this->kuzzle->addHeaders($data, $this->headers),
            $options
        );

        return $response['result']['count'];
    }


    /**
     * Create a new empty data collection, with no associated mapping.
     *
     * @param array $options Optional parameters
     * @return DataCollection
     */
    public function create(array $options = [])
    {
        $this->kuzzle->query(
            $this->buildQueryArgs('write', 'createCollection'),
            $this->kuzzle->addHeaders([], $this->headers),
            $options
        );

        return $this;
    }

    /**
     * Create a new document in Kuzzle.
     *
     * @param array|Document $document either an instance of a KuzzleDocument object, or a document
     * @param string $id document identifier
     * @param array $options Optional parameters
     *
     * @return Document
     */
    public function createDocument($document, $id = '', array $options = [])
    {
        $action = 'create';
        $data = [];
        
        if (array_key_exists('updateIfExist', $options)) {
            $action = $options['updateIfExist'] ? 'createOrUpdate' : 'create';
        }

        if ($document instanceof Document)
        {
            $data = $document->serialize();
        }
        else
        {
            $data['body'] = $document;
        }

        if (!empty($id))
        {
            $data['_id'] = $id;
        }
        
        $response = $this->kuzzle->query(
            $this->buildQueryArgs('write', $action),
            $this->kuzzle->addHeaders($data, $this->headers),
            $options
        );

        return new Document($this, $response['result']['_id'], $response['result']['_source']);
    }

    /**
     * Creates a new KuzzleDataMapping object, using its constructor.
     *
     * @param array $mapping Optional mapping
     * @return DataMapping
     */
    public function dataMappingFactory(array $mapping = [])
    {
        return new DataMapping($this, $mapping);
    }

    /**
     * Delete either a stored document, or all stored documents matching search filters.
     *
     * @param array|string $filters Unique document identifier OR Filters in ElasticSearch Query DSL format
     * @param array $options Optional parameters
     * @return integer|integer[]
     */
    public function deleteDocument($filters, array $options = [])
    {
        $data = [];

        if (is_string($filters))
        {
            $data['_id'] = $filters;
            $action = 'delete';
        }
        else
        {
            $data['body'] = $filters;
            $action = 'deleteByQuery';
        }

        $response = $this->kuzzle->query(
            $this->buildQueryArgs('write', $action),
            $this->kuzzle->addHeaders($data, $this->headers),
            $options
        );

        return $action === 'delete' ? $response['result']['_id'] : $response['result']['ids'];
    }

    /**
     * Creates a new KuzzleDocument object, using its constructor.
     *
     * @param string $id Optional document unique ID
     * @param array $content Optional document content
     * @return Document the newly created Kuzzle\Document object
     */
    public function documentFactory($id = '', array $content = [])
    {
        return new Document($this, $id, $content);
    }

    /**
     * Retrieves a single stored document using its unique document ID.
     *
     * @param string $documentId Unique document identifier
     * @param array $options Optional parameters
     * @return Document
     */
    public function fetchDocument($documentId, array $options = [])
    {
        $data = [
            '_id' => $documentId
        ];

        $response = $this->kuzzle->query(
            $this->buildQueryArgs('read', 'get'),
            $this->kuzzle->addHeaders($data, $this->headers),
            $options
        );

        $content = $response['result']['_source'];
        $content['_version'] = $response['result']['_version'];

        return new Document($this, $response['result']['_id'], $content);
    }

    /**
     * Retrieves all documents stored in this data collection.
     *
     * @param array $options Optional parameters
     * @return array containing the total number of retrieved documents and an array of Kuzzle/Document objects
     */
    public function fetchAllDocuments(array $options = [])
    {
        $filters = [];
        
        if (array_key_exists('from', $options))
        {
            $filters['from'] = $options['from'];
        }

        if (array_key_exists('size', $options))
        {
            $filters['size'] = $options['size'];
        }

        return $this->advancedSearch($filters, $options);
    }

    /**
     * Retrieves the current mapping of this collection.
     *
     * @param array $options Optional parameters
     * @return DataMapping
     */
    public function getMapping(array $options = [])
    {
        return $this->dataMappingFactory()->refresh($options);
    }

    /**
     * Replace an existing document with a new one.
     *
     * @param string $documentId Unique document identifier
     * @param array $content Content of the document to create
     * @param array $options Optional parameters
     * @return Document
     */
    public function replaceDocument($documentId, array $content, array $options = [])
    {
        $data = [
            '_id' => $documentId,
            'body' => $content
        ];

        $response = $this->kuzzle->query(
            $this->buildQueryArgs('write', 'createOrReplace'),
            $this->kuzzle->addHeaders($data, $this->headers),
            $options
        );

        $content = $response['result']['_source'];
        $content['_version'] = $response['result']['_version'];

        return new Document($this, $response['result']['_id'], $content);
    }

    /**
     * This is a helper function returning itself, allowing to easily set headers while chaining calls.
     *
     * @param array $headers New content
     * @param bool $replace true: replace the current content with the provided data, false: merge it
     * @return DataCollection
     */
    public function setHeaders(array $headers, $replace = false)
    {
        if ($replace)
        {
            $this->headers = $headers;
        }
        else
        {
            foreach ($headers as $key => $value)
            {
                $this->headers[$key] = $value;
            }
        }

        return $this;
    }

    /**
     * Truncate the data collection,
     * removing all stored documents but keeping all associated mappings.
     *
     * @param array $options Optional parameters
     * @return DataCollection
     */
    public function truncate(array $options = [])
    {
        $this->kuzzle->query(
            $this->buildQueryArgs('admin', 'truncateCollection'),
            $this->kuzzle->addHeaders([], $this->headers),
            $options
        );

        return $this;
    }

    /**
     * Update parts of a document, by replacing some fields or adding new ones.
     * Note that you cannot remove fields this way: missing fields will simply be left unchanged.
     *
     * @param string $documentId Unique document identifier
     * @param array $content Content of the document to create
     * @param array $options Optional parameters
     * @return Document
     */
    public function updateDocument($documentId, array $content, array $options = [])
    {
        $data = [
            '_id' => $documentId,
            'body' => $content
        ];

        $response = $this->kuzzle->query(
            $this->buildQueryArgs('write', 'update'),
            $this->kuzzle->addHeaders($data, $this->headers),
            $options
        );

        return (new Document($this, $response['result']['_id']))->refresh();
    }

    /**
     * @return Kuzzle
     */
    public function getKuzzle()
    {
        return $this->kuzzle;
    }

    /**
     * @return string
     */
    public function getCollectionName()
    {
        return $this->collection;
    }

    /**
     * @return string
     */
    public function getIndexName()
    {
        return $this->index;
    }

    /**
     * @param $controller
     * @param $action
     * @return array
     */
    public function buildQueryArgs($controller, $action)
    {
        return $this->kuzzle->buildQueryArgs(
            $controller,
            $action,
            $this->index,
            $this->collection
        );
    }
}