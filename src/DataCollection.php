<?php

namespace Kuzzle;

use Kuzzle\Util\SearchResult;

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
     * @param string $collection The name of the data collection you want to manipulate
     * @param string $index Name of the index containing the data collection
     */
    public function __construct(Kuzzle $kuzzle, $collection, $index)
    {
        $this->kuzzle = $kuzzle;
        $this->index = $index;
        $this->collection = $collection;

        return $this;
    }

    /**
     * Executes an advanced search on the data collection.
     *
     * @deprecated
     * @see search
     *
     * @param array $filters Filters in ElasticSearch Query DSL format
     * @param array $options Optional parameters
     * @return SearchResult
     */
    public function advancedSearch(array $filters, array $options = [])
    {
        trigger_error('Usage of Kuzzle\\DataCollection::advancedSearch is deprecated. Use Kuzzle\\DataCollection::search instead', E_USER_WARNING);

        return $this->search($filters, $options);
    }

    /**
     * Executes an advanced search on the data collection.
     *
     * @param array $filters Filters in ElasticSearch Query DSL format
     * @param array $options Optional parameters
     * @return SearchResult
     */
    public function search(array $filters, array $options = [])
    {
        $data = [
            'body' => $filters
        ];

        $response = $this->kuzzle->query(
            $this->buildQueryArgs('document', 'search'),
            $this->kuzzle->addHeaders($data, $this->headers),
            $options
        );

        $response['result']['hits'] = array_map(function ($document) {
            return new Document($this, $document['_id'], $document['_source']);
        }, $response['result']['hits']);


        if (array_key_exists('_scroll_id', $response['result'])) {
            $options['scrollId'] = $response['result']['_scroll_id'];
        }

        return new SearchResult(
            $this,
            $response['result']['total'],
            $response['result']['hits'],
            array_key_exists('aggregations', $response['result']) ? $response['result']['aggregations'] : [],
            ['filters' => $filters, 'options' => $options]
        );
    }


    /**
     * Retrieves next result of a search with scroll query.
     *
     * @param string $scrollId
     * @param array $options (optional) arguments
     * @param array $filters (optional) original filters
     * @return SearchResult
     */
    public function scroll($scrollId, array $options = [], array $filters = [])
    {
        $options['httpParams'] = [':scrollId' => $scrollId];

        $data = [];

        $response = $this->kuzzle->query(
            $this->kuzzle->buildQueryArgs('document', 'scroll'),
            $data,
            $options
        );

        $response['result']['hits'] = array_map(function ($document) {
            return new Document($this, $document['_id'], $document['_source']);
        }, $response['result']['hits']);


        if (array_key_exists('_scroll_id', $response['result'])) {
            $options['scrollId'] = $response['result']['_scroll_id'];
        }

        return new SearchResult(
            $this,
            $response['result']['total'],
            $response['result']['hits'],
            array_key_exists('aggregations', $response['result']) ? $response['result']['aggregations'] : [],
            ['filters' => $filters, 'options' => $options]
        );
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
            $this->buildQueryArgs('document', 'count'),
            $this->kuzzle->addHeaders($data, $this->headers),
            $options
        );

        return $response['result']['count'];
    }


    /**
     * Create a new empty data collection, with no associated mapping.
     *
     * @param array $options Optional parameters
     * @return boolean
     */
    public function create(array $options = [])
    {
        $response = $this->kuzzle->query(
            $this->buildQueryArgs('collection', 'create'),
            $this->kuzzle->addHeaders([], $this->headers),
            $options
        );

        return $response['result']['acknowledged'];
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
            $action = $options['updateIfExist'] ? 'createOrReplace' : 'create';
        }

        if ($document instanceof Document) {
            $data = $document->serialize();
        } else {
            $data['body'] = $document;
        }

        if (!empty($id)) {
            $data['_id'] = $id;
        }

        $response = $this->kuzzle->query(
            $this->buildQueryArgs('document', $action),
            $this->kuzzle->addHeaders($data, $this->headers),
            $options
        );

        $content = $response['result']['_source'];
        $content['_version'] = $response['result']['_version'];

        return new Document($this, $response['result']['_id'], $content);
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

        if (is_string($filters)) {
            $data['_id'] = $filters;
            $action = 'delete';
        } else {
            $data['body'] = $filters;
            $action = 'deleteByQuery';
        }

        $response = $this->kuzzle->query(
            $this->buildQueryArgs('document', $action),
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
            $this->buildQueryArgs('document', 'get'),
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
     * @return Document[] containing all documents objects
     */
    public function fetchAllDocuments(array $options = [])
    {
        $documents = [];
        $filters = [];

        if (!array_key_exists('from', $options)) {
            $options['from'] = 0;
        }

        if (!array_key_exists('size', $options)) {
            $options['size'] = 1000;
        }

        $searchResult = $this->search($filters, $options);

        if ($searchResult->getTotal() > 10000) {
            trigger_error('Usage of Kuzzle\\DataCollection::fetchAllDocuments will fetch more than 10 000 document. To avoid performance issues, please use Kuzzle\\DataCollection::search and Kuzzle\\DataCollection::scroll requests', E_USER_WARNING);
        }

        while ($searchResult) {
            foreach ($searchResult->getDocuments() as $document) {
                $documents[] = $document;
            }
            $searchResult = $searchResult->getNext();
        }

        return $documents;
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
     * Create a new document in Kuzzle.
     *
     * @param array|Document $document either an instance of a KuzzleDocument object, or a document
     * @param array $options Optional parameters
     *
     * @return bool
     */
    public function publishMessage($document, array $options = [])
    {
        $data = [];

        if ($document instanceof Document) {
            $data = $document->serialize();
        } else {
            $data['body'] = $document;
        }

        $response = $this->kuzzle->query(
            $this->buildQueryArgs('realtime', 'publish'),
            $this->kuzzle->addHeaders($data, $this->headers),
            $options
        );

        return $response['result']['published'];
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
            $this->buildQueryArgs('document', 'createOrReplace'),
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
     * Truncate the data collection,
     * removing all stored documents but keeping all associated mappings.
     *
     * @param array $options Optional parameters
     * @return array ids of deleted documents
     */
    public function truncate(array $options = [])
    {
        $response = $this->kuzzle->query(
            $this->buildQueryArgs('collection', 'truncate'),
            $this->kuzzle->addHeaders([], $this->headers),
            $options
        );

        return $response['result']['ids'];
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

        $queryArgs = $this->buildQueryArgs('document', 'update');
        $queryArgs['route'] = '/' . $this->index . '/' . $this->collection . '/' . $documentId . '/_update';
        $queryArgs['method'] = 'put';

        $response = $this->kuzzle->query(
            $queryArgs,
            $this->kuzzle->addHeaders($data, $this->headers),
            $options
        );

        return (new Document($this, $response['result']['_id']))->refresh($options);
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
