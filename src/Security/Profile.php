<?php

namespace Kuzzle\Security;

/**
 * Class Profile
 * @package kuzzleio/kuzzle-sdk
 */
class Profile extends Document
{
    protected $deleteActionName = 'deleteProfile';

    protected $updateActionName = 'createOrReplaceProfile';

    protected $saveActionName = 'updateProfile';

    /**
     * Role constructor.
     * @param Security $kuzzleSecurity An instantiated Kuzzle\Security object
     * @param string $id Unique profile identifier
     * @param array $content Profile content
     * @return Profile
     */
    public function __construct(Security $kuzzleSecurity, $id = '', array $content = [])
    {
        parent::__construct($kuzzleSecurity, $id, $content);

        if (!array_key_exists('roles', $this->content)) {
            $this->content['roles'] = [];
        }

        /*
         * Remove roles data to keep only theirs ids
         * @todo: refactor this at repository refactor
         */
        $this->content['roles'] = array_map(function ($role) {
            return $this->extractRoleId($role);
        }, $this->content['roles']);

        return $this;
    }

    /**
     * Adds a role to the profile.
     *
     * @param string|Role $role Unique id or Kuzzle\Security\Role instance corresponding to the new associated role
     * @return Profile
     */
    public function addRole($role)
    {
        $this->content['roles'][] = $this->extractRoleId($role);

        return $this;
    }

    /**
     * Returns this profile associated roles.
     *
     * @return Role[]
     */
    public function getRoles()
    {
        $roles = [];

        array_walk($this->content['roles'], function ($role) {
            $roles[] = $this->security->getRole($role);
        });

        return $roles;
    }

    /**
     * Replaces the roles associated to the profile.
     *
     * @param string[]|Role[] $roles List of unique id or Kuzzle\Security\Role instances corresponding to the new associated roles
     * @return Profile
     */
    public function setRoles(array $roles)
    {
        /*
         * @todo: refactor this at repository refactor
         */
        $this->content['roles'] = array_map(function ($role) {
            return $this->extractRoleId($role);
        }, $roles);

        return $this;
    }

    /**
     * @param Role|array|string $role
     * @return string
     * @todo: refactor this at repository refactor
     */
    protected function extractRoleId($role)
    {
        if (is_array($role) && array_key_exists('_id', $role)) {
            $role = $role['_id'];
        }

        if ($role instanceof Role) {
            $role = $role->getId();
        }

        return $role;
    }
}
