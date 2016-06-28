<?php

namespace Kuzzle\Security;

Abstract Class Document
{
    protected $deleteActionName = '';

    protected $updateActionName = '';

    protected $saveActionName = '';

    /**
     * @var Security The kuzzle security instance associated to this document
     */
    protected $security;

    /**
     * @var string Unique document identifier
     */
    protected $id;

    /**
     * @var array The content of the document
     */
    protected $content;

    /**
     * Document constructor.
     *
     * @param Security $kuzzleSecurity An instantiated Kuzzle\Security object
     * @param string $id Unique document identifier
     * @param array $content Document content
     * @return Document
     */
    public function __construct(Security $kuzzleSecurity, $id = '', array $content)
    {
        $this->security = $kuzzleSecurity;
        $this->id = $id;
        $this->content = $content;

        return $this;
    }


    /**
     * Replaces the content of the Kuzzle\Security\Role object.
     *
     * @param array $content
     * @return Document
     */
    public function setContent(array $content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Performs a partial content update on this object.
     *
     * @param array $content New profile content
     * @param array $options Optional parameters
     * @return Profile
     */
    public function update(array $content, array $options = [])
    {
        $data = [
            '_id' => $this->id,
            'body' => $content
        ];

        $response = $this->security->getKuzzle()->query(
            $this->security->buildQueryArgs($this->updateActionName),
            $data,
            $options
        );

        $this->setContent($response['result']['_source']);

        return $this;
    }

    /**
     * Creates or replaces the profile in Kuzzle’s database layer.
     *
     * @param array $options Optional parameters
     * @return Profile
     */
    public function save(array $options = [])
    {
        $data = $this->serialize();

        $this->security->getKuzzle()->query(
            $this->security->buildQueryArgs($this->saveActionName),
            $data,
            $options
        );

        return $this;
    }

    /**
     * Deletes the profile from Kuzzle’s database layer.
     *
     * @param array $options Optional parameters
     * @return string the id of deleted profile
     */
    public function delete(array $options = [])
    {
        $data = [
            '_id' => $this->id
        ];

        $response = $this->security->getKuzzle()->query(
            $this->security->buildQueryArgs($this->deleteActionName),
            $data,
            $options
        );

        return $response['result']['_id'];
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

        $data['body'] = $this->content;


        return $data;
    }

}