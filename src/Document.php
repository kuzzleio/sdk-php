<?php

namespace Kuzzle;

use ErrorException;

/**
 * Class Document
 * @package kuzzleio/kuzzle-sdk
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
     * @return Document
     */
    public function __construct(DataCollection $kuzzleDataCollection, $documentId = '', array $content = [])
    {
        $this->collection = $kuzzleDataCollection;

        if (!empty($documentId)) {
            $this->id = $documentId;
        }

        if (!empty($content)) {
            if (array_key_exists('_version', $content)) {
                $this->version = $content['_version'];
                unset($content['_version']);
            }

            $this->setContent($content, true);
        }

        return $this;
    }

    /**
     * Deletes this document in Kuzzle
     *
     * @param array $options Optional parameters
     * @return integer Id of document deleted
     *
     * @throws ErrorException
     */
    public function delete(array $options = [])
    {
        if (!$this->id) {
            throw new ErrorException('Kuzzle\Document::delete: cannot delete a document without a document ID');
        }

        $this->collection->getKuzzle()->query(
            $this->collection->buildQueryArgs('read', 'search'),
            $this->collection->getKuzzle()->addHeaders($this->serialize(), $this->headers),
            $options
        );

        return $this->id;
    }

    /**
     * Creates a new KuzzleDocument object with the last version of this document stored in Kuzzle.
     *
     * @param array $options Optional parameters
     * @return Document
     *
     * @throws ErrorException
     */
    public function refresh(array $options = [])
    {
        if (!$this->id) {
            throw new ErrorException('Kuzzle\Document::delete: cannot retrieve a document without a document ID');
        }

        $data = [
            '_id' => $this->id
        ];

        $response = $this->collection->getKuzzle()->query(
            $this->collection->buildQueryArgs('read', 'get'),
            $this->collection->getKuzzle()->addHeaders($data, $this->headers),
            $options
        );

        $content = $response['result']['_source'];
        $content['_version'] = $response['result']['_version'];

        return new Document($this->collection, $response['result']['_id'], $content);
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
        $response = $this->collection->getKuzzle()->query(
            $this->collection->buildQueryArgs('write', 'createOrReplace'),
            $this->collection->getKuzzle()->addHeaders($this->serialize(), $this->headers),
            $options
        );

        $this->id = $response['result']['_id'];
        $this->version = $response['result']['_version'];

        return $this;
    }

    /**
     * Replaces the current content with new data.
     * This is a helper function returning itself, allowing to easily chain calls.
     *
     * @param array $content
     * @param bool $replace true: replace the current content with the provided data, false: merge it
     * @return Document
     */
    public function setContent(array $content, $replace = false)
    {
        if ($replace) {
            $this->content = $content;
        } else {
            foreach ($content as $key => $value) {
                $this->content[$key] = $value;
            }
        }

        return $this;
    }

    /**
     * This is a helper function returning itself, allowing to easily chain calls.
     *
     * @param array $headers New content
     * @param bool $replace true: replace the current content with the provided data, false: merge it
     * @return Document
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
    public function serialize()
    {
        $data = [];

        if (!empty($this->id)) {
            $data['_id'] = $this->id;
        }

        if (!empty($this->version)) {
            $data['_version'] = $this->version;
        }

        $data['body'] = $this->content;


        return $data;
    }
}