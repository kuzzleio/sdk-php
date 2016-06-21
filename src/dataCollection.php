<?php

namespace Kuzzle;

/**
 * Class DataCollection
 * @package kuzzle-sdk
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
    function __construct(Kuzzle $kuzzle, $index, $collection)
    {

    }

    /**
     * Executes an advanced search on the data collection.
     *
     * @param array $filters Filters in ElasticSearch Query DSL format
     * @param array $options Optional parameters
     */
    function advancedSearch(array $filters, array $options = [])
    {

    }

    /**
     * Returns the number of documents matching the provided set of filters.
     *
     * @param array $filters Filters in ElasticSearch Query DSL format
     * @param array $options Optional parameters
     *
     * @return integer the matched documents count
     */
    function count(array $filters, array $options = [])
    {

    }


    /**
     * Create a new empty data collection, with no associated mapping.
     *
     * @param array $options Optional parameters
     */
    function create(array $options = [])
    {

    }

    /**
     * Create a new document in Kuzzle.
     *
     * @param Document $kuzzleDocument KuzzleDocument object
     * @param array $options Optional parameters
     *
     * @return Document
     */
    function createDocument(Document $kuzzleDocument, array $options = [])
    {

    }

    /**
     * Creates a new KuzzleDataMapping object, using its constructor.
     *
     * @param array $mapping Optional mapping
     */
    function dataMappingFactory(array $mapping = [])
    {

    }

    /**
     * Delete either a stored document, or all stored documents matching search filters.
     *
     * @param array|string $filters Unique document identifier OR Filters in ElasticSearch Query DSL format
     * @param array $options Optional parameters
     */
    function deleteDocument($filters, array $options = [])
    {

    }

    /**
     * Creates a new KuzzleDocument object, using its constructor.
     *
     * @param string $id Optional document unique ID
     * @param array $content Optional document content
     * @return Document the newly created Kuzzle\Document object
     */
    function documentFactory($id = '', array $content = [])
    {

    }

    /**
     * Retrieves a single stored document using its unique document ID.
     *
     * @param string $documentId Unique document identifier
     * @param array $options Optional parameters
     * @return Document
     */
    function fetchDocument($documentId, array $options = [])
    {

    }

    /**
     * Retrieves all documents stored in this data collection.
     *
     * @param array $options Optional parameters
     * @return array containing the total number of retrieved documents and an array of Kuzzle/Document objects
     */
    function fetchAllDocuments(array $options = [])
    {

    }

    /**
     * Retrieves the current mapping of this collection.
     *
     * @param array $options Optional parameters
     * @return DataMapping
     */
    function getMapping(array $options = [])
    {

    }

    /**
     * Replace an existing document with a new one.
     *
     * @param string $documentId Unique document identifier
     * @param array $content Content of the document to create
     * @param array $options Optional parameters
     * @return Document
     */
    function replaceDocument($documentId, array $content, array $options = [])
    {

    }

    /**
     * This is a helper function returning itself, allowing to easily set headers while chaining calls.
     *
     * @param array $content New content
     * @param bool $replace true: replace the current content with the provided data, false: merge it
     * @return DataCollection
     */
    function setHeaders(array $content, $replace = false)
    {

        return $this;
    }

    /**
     * Truncate the data collection,
     * removing all stored documents but keeping all associated mappings.
     *
     * @param array $options Optional parameters
     */
    function truncate(array $options = [])
    {

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
    function updateDocument($documentId, array $content, array $options = [])
    {

    }
}