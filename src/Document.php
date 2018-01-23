<?php

namespace Kuzzle;

use InvalidArgumentException;

/**
 * Class Document
 * @package kuzzleio/kuzzle-sdk
 */
class Document
{
    /**
     * @var Collection The data collection associated to this document
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
     * @var array Document metadata
     */
    protected $meta;

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
     * @param Collection $kuzzleDataCollection An instantiated KuzzleDataCollection object
     * @param string $documentId ID of an existing document.
     * @param array $content Initializes this document with the provided content
     * @param array $meta Initializes this document with the provided metadata
     * @return Document
     */
    public function __construct(Collection $kuzzleDataCollection, $documentId = '', array $content = [], array $meta = [])
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

        if (!empty($meta)) {
            $this->meta = $meta;
        }

        return $this;
    }

    /**
     * Deletes this document in Kuzzle
     *
     * @param array $options Optional parameters
     * @return integer Id of document deleted
     *
     * @throws InvalidArgumentException
     */
    public function delete(array $options = [])
    {
        if (!$this->id) {
            throw new InvalidArgumentException('Kuzzle\Document::delete: cannot delete a document without a document ID');
        }

        $this->collection->getKuzzle()->query(
            $this->collection->buildQueryArgs('document', 'delete'),
            $this->collection->getKuzzle()->addHeaders($this->serialize(), $this->headers),
            $options
        );

        return $this->id;
    }

    /**
     * Checks if this document exists in Kuzzle.
     *
     * @param array $options
     * @return boolean
     *
     * @throws InvalidArgumentException
     */
    public function exists(array $options = [])
    {
        if (!$this->id) {
            throw new InvalidArgumentException('Kuzzle\Document::exists: cannot check if the document exists without a document ID');
        }

        $response = $this->collection->getKuzzle()->query(
            $this->collection->buildQueryArgs('document', 'exists'),
            $this->collection->getKuzzle()->addHeaders($this->serialize(), $this->headers),
            $options
        );

        return  $response['result'];
    }

    /**
     * Creates a new KuzzleDocument object with the last version of this document stored in Kuzzle.
     *
     * @param array $options Optional parameters
     * @return Document
     *
     * @throws InvalidArgumentException
     */
    public function refresh(array $options = [])
    {
        if (!$this->id) {
            throw new InvalidArgumentException('Kuzzle\Document::refresh: cannot retrieve a document without a document ID');
        }

        $data = [
            '_id' => $this->id
        ];

        $response = $this->collection->getKuzzle()->query(
            $this->collection->buildQueryArgs('document', 'get'),
            $this->collection->getKuzzle()->addHeaders($data, $this->headers),
            $options
        );

        $documentContent = $response['result']['_source'];
        $documentContent['_version'] = $response['result']['_version'];
        $documentMeta = $response['result']['_meta'];

        return new Document($this->collection, $response['result']['_id'], $documentContent, $documentMeta);
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
            $this->collection->buildQueryArgs('document', 'createOrReplace'),
            $this->collection->getKuzzle()->addHeaders($this->serialize(), $this->headers),
            $options
        );

        $this->id = $response['result']['_id'];
        $this->version = $response['result']['_version'];

        return $this;
    }

    /**
     * Sends the content of this document as a realtime message.
     *
     * Takes an optional argument object with the following properties:
     *    - volatile (object, default: null):
     *        Additional information passed to notifications to other users
     *
     * @param array $options
     * @return Document
     */
    public function publish(array $options = [])
    {
        $this->collection->getKuzzle()->query(
            $this->collection->buildQueryArgs('realtime', 'publish'),
            $this->collection->getKuzzle()->addHeaders($this->serialize(), $this->headers),
            $options
        );

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
     * @return array
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @return array
     */
    public function getMeta()
    {
        return $this->meta;
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
    public function getHeaders()
    {
        return $this->headers;
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
        $data['meta'] = $this->meta;

        return $data;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return integer
     */
    public function getVersion()
    {
        return $this->version;
    }
}
