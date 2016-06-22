<?php

namespace Kuzzle;

/**
 * Class Document
 * @package kuzzle-sdk
 */
class Document
{
    /**
     * @var DataCollection The data collection associated to this document
     */
    protected $collection;

    /**
     * @var array The content of the document
     */
    protected $content;

    /**
     * @var array Common headers for all sent documents.
     */
    protected $headers = [];

    /**
     * @var string Unique document identifier
     */
    protected $id;

    /**
     * @var integer Current document version
     */
    protected $version;

    /**
     * Document constructor.
     *
     * @param DataCollection $kuzzleDataCollection An instantiated KuzzleDataCollection object
     * @param string $documentId ID of an existing document.
     * @param array $content Initializes this document with the provided content
     */
    public function __construct(DataCollection $kuzzleDataCollection, $documentId = '', array $content = [])
    {

    }

    /**
     * Deletes this document in Kuzzle
     *
     * @param array $options Optional parameters
     */
    public function delete(array $options = [])
    {

    }

    /**
     * Creates a new KuzzleDocument object with the last version of this document stored in Kuzzle.
     *
     * @param array $options Optional parameters
     * @return Document
     */
    public function refresh(array $options = [])
    {

    }

    /**
     * Saves this document into Kuzzle.
     *
     * If this is a new document, this function will create it in Kuzzle and the id property will be made available.
     * Otherwise, this method will replace the latest version of this document in Kuzzle by the current content of this object.
     *
     * @param array $options Optional parameters
     * @return Document
     */
    public function save(array $options = [])
    {

    }

    /**
     * Replaces the current content with new data.
     * This is a helper function returning itself, allowing to easily chain calls.
     *
     * @param array $data
     * @param array $options
     * @return Document
     */
    public function setContent(array $data, array $options = [])
    {

        return $this;
    }

    /**
     * This is a helper function returning itself, allowing to easily chain calls.
     *
     * @param array $content New content
     * @param bool $replace true: replace the current content with the provided data, false: merge it
     */
    public function setHeaders(array $content, $replace = false)
    {

    }
}