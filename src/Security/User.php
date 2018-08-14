<?php

namespace Kuzzle\Security;

use BadMethodCallException;

/**
 * Class User
 * @package kuzzleio/kuzzle-sdk
 */
class User
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
     * User constructor.
     *
     * @param Security $kuzzleSecurity An instantiated Kuzzle\Security object
     * @param string $id Unique user identifier
     * @param array $content User content
     * @param array $meta User metadata
     * @return User
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
