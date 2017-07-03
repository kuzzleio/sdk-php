<?php

namespace Kuzzle\Security;

/**
 * Class Role
 * @package kuzzleio/kuzzle-sdk
 */
class Role extends Document
{
    protected $deleteActionName = 'deleteRole';

    protected $updateActionName = 'updateRole';

    protected $saveActionName = 'createOrReplaceRole';

    /**
     * Role constructor.
     *
     * @param Security $kuzzleSecurity An instantiated Kuzzle\Security object
     * @param string $id Unique role identifier
     * @param array $content Role content
     * @param array $meta Role metadata
     */
    public function __construct(Security $kuzzleSecurity, $id = '', array $content = [], array $meta = [])
    {
        parent::__construct($kuzzleSecurity, $id, $content, $meta);

        return $this;
    }
}
