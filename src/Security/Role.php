<?php

namespace Kuzzle\Security;

/**
 * Class Role
 * @package kuzzleio/kuzzle-sdk
 */
class Role
{
    /**
     * @var Security The kuzzle security instance associated to this document
     */
    public $security;

    /**
     * @var string Unique document identifier
     */
    public $id;

    /**
     * @var array The content of the document
     */
    public $content;

    /**
     * @var array The metadata of the document
     */
    public $meta;

    /**
     * Role constructor.
     *
     * @param Security $kuzzleSecurity An instantiated Kuzzle\Security object
     * @param string $id Unique role identifier
     * @param array $content Role content
     * @param array $meta Role metadata
     * @return Role
     */
    public function __construct(Security $kuzzleSecurity, $id = '', array $content = [], array $meta = [])
    {
        $this->security = $kuzzleSecurity;
        $this->id = $id;
        $this->content = $content;
        $this->meta = $meta;

        return $this;
    }
}
