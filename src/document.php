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
    function __construct(DataCollection $kuzzleDataCollection, $documentId = '', array $content = [])
    {

    }

}