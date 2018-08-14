<?php

namespace Kuzzle\Security;

use InvalidArgumentException;

/**
 * Class Profile
 * @package kuzzleio/kuzzle-sdk
 */
class Profile
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
     * Profile constructor.
     * @param Security $kuzzleSecurity An instantiated Kuzzle\Security object
     * @param string $id Unique profile identifier
     * @param array $content Profile content
     * @param array $meta Profile metadata
     * @return Profile
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
