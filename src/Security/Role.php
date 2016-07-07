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
     * @return Role
     */
    public function __construct(Security $kuzzleSecurity, $id = '', array $content = [])
    {
        parent::__construct($kuzzleSecurity, $id, $content);

        return $this;
    }
}
