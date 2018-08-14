<?php

namespace Kuzzle\Security;

use ErrorException;
use InvalidArgumentException;
use Kuzzle\Kuzzle;
use Kuzzle\Util\ProfilesSearchResult;
use Kuzzle\Util\RolesSearchResult;
use Kuzzle\Util\UsersSearchResult;

/**
 * Class Security
 * @package kuzzleio/kuzzle-sdk
 */
class Security
{
    /**
     * @var Kuzzle
     */
    public $kuzzle;

    /**
     * Security controller constructor.
     * @param Kuzzle $kuzzle An instantiated Kuzzle object
     * @return Security
     */
    public function __construct(Kuzzle $kuzzle)
    {
        $this->kuzzle = $kuzzle;

        return $this;
    }

    /**
     * Create a new profile in Kuzzle.
     *
     * @param string $id Unique profile identifier
     * @param array $policies List of policies to apply to this profile
     * @param array $options Optional arguments
     *
     * @return Profile
     *
     * @throws InvalidArgumentException
     */
    public function createProfile($id, array $policies, array $options = [])
    {
        if (empty($id) || empty($policies)) {
            throw new InvalidArgumentException('Kuzzle\Security::createProfile: Unable to create profile: no id or policies specified');
        }

        $options['httpParams'] = [':_id' => $id];


        $response = $this->kuzzle->query(
            $this->kuzzle->buildQueryArgs('security', 'createProfile'),
            [
                '_id' => $id,
                'body' => json_encode([ 'policies' => $policies ])
            ],
            $options
        );

        return new Profile($this, $response['result']['_id'], $response['result']['_source'], $response['result']['_meta']);
    }

    /**
     * Create or replace a profile in Kuzzle.
     *
     * @param string $id Unique profile identifier
     * @param array $policies List of policies to apply to this profile
     * @param array $options Optional arguments
     *
     * @return Profile
     *
     * @throws InvalidArgumentException
     */
    public function createOrReplaceProfile($id, array $policies, array $options = [])
    {
        if (empty($id) || empty($policies)) {
            throw new InvalidArgumentException('Kuzzle\Security::createOrReplaceProfile: Unable to create or replace profile: no id or policies specified');
        }

        $options['httpParams'] = [':_id' => $id];

        $response = $this->kuzzle->query(
            $this->kuzzle->buildQueryArgs('security', 'createOrReplaceProfile'),
            [
                '_id' => $id,
                'body' => json_encode([ 'policies' => $policies ])
            ],
            $options
        );

        return new Profile($this, $response['result']['_id'], $response['result']['_source'], $response['result']['_meta']);
    }

    /**
     * Create a new role in Kuzzle.
     *
     * @param integer $id Unique role identifier
     * @param array $content Data representing the role
     * @param array $options Optional arguments
     *
     * @return Role
     *
     * @throws InvalidArgumentException
     */
    public function createRole($id, array $content, array $options = [])
    {
        if (empty($id) || empty($content)) {
            throw new InvalidArgumentException('Kuzzle\Security::createRole: Unable to create role: no id or content specified');
        }

        $options['httpParams'] = [':_id' => $id];

        $response = $this->kuzzle->query(
            $this->kuzzle->buildQueryArgs('security', 'createRole'),
            [
                '_id' => $id,
                'body' => json_encode($content)
            ],
            $options
        );

        return new Role($this, $response['result']['_id'], $response['result']['_source'], $response['result']['_meta']);
    }

    /**
     * Create or replace a role in Kuzzle.
     *
     * @param integer $id Unique role identifier
     * @param array $content Data representing the role
     * @param array $options Optional arguments
     *
     * @return Role
     *
     * @throws InvalidArgumentException
     */
    public function createOrReplaceRole($id, array $content, array $options = [])
    {
        if (empty($id) || empty($content)) {
            throw new InvalidArgumentException('Kuzzle\Security::createOrReplaceRole: Unable to create or replace role: no id or content specified');
        }

        $options['httpParams'] = [':_id' => $id];

        $response = $this->kuzzle->query(
            $this->kuzzle->buildQueryArgs('security', 'createOrReplaceRole'),
            [
                '_id' => $id,
                'body' => json_encode($content)
            ],
            $options
        );

        return new Role($this, $response['result']['_id'], $response['result']['_source'], $response['result']['_meta']);
    }

    /**
     * Create a new user in Kuzzle.
     *
     * @param integer $id Unique user identifier, will be used as username
     * @param array $content Data representing the user
     * @param array $options Optional arguments
     *
     * @return User
     *
     * @throws InvalidArgumentException
     */
    public function createUser($id, array $content, array $options = [])
    {
        if (empty($id) || empty($content)) {
            throw new InvalidArgumentException('Kuzzle\Security::createUser: Unable to create user: no id or content specified');
        }

        $options['httpParams'] = [':_id' => $id];

        $response = $this->kuzzle->query(
            $this->kuzzle->buildQueryArgs('security', 'createUser'),
            [
                '_id' => $id,
                'body' => json_encode($content)
            ],
            $options
        );

        return new User($this, $response['result']['_id'], $response['result']['_source'], $response['result']['_meta']);
    }

    /**
     * Create a new restricted user in Kuzzle.
     *
     * @param integer $id Unique user identifier, will be used as username
     * @param array $content Data representing the user
     * @param array $options Optional arguments
     *
     * @return User
     *
     * @throws InvalidArgumentException
     */
    public function createRestrictedUser($id, array $content, array $options = [])
    {
        if (empty($id) || empty($content)) {
            throw new InvalidArgumentException('Kuzzle\Security::createRestrictedUser: Unable to create restricted user: no id or content specified');
        }

        $options['httpParams'] = [':_id' => $id];

        $response = $this->kuzzle->query(
            $this->kuzzle->buildQueryArgs('security', 'createRestrictedUser'),
            [
                '_id' => $id,
                'body' => json_encode($content)
            ],
            $options
        );

        return new User($this, $response['result']['_id'], $response['result']['_source'], $response['result']['_meta']);
    }

    /**
     * Creates the first admin user in Kuzzle.
     * Does nothing if an admin user already exists.
     *
     * @param integer $id Unique user identifier, will be used as username
     * @param bool $reset If the optional field reset is set to true
     *    (1 with http), the preset roles (anonymous and default)
     *    will be reset with more restrictive rights.
     *    Set to false by default.
     * @param array $content Data representing the user
     * @param array $options Optional arguments
     *
     * @return User
     *
     * @throws InvalidArgumentException
     */
    public function createFirstAdmin($reset = false, array $content = [], array $options = [])
    {
        if (empty($content)) {
            throw new InvalidArgumentException('Kuzzle\Security::createFirstAdmin: Unable to create first admin: no id or content specified');
        }

        $response = $this->kuzzle->query(
            $this->kuzzle->buildQueryArgs('security', 'createFirstAdmin'),
            [
                'reset' => $reset,
                'body' => json_encode($content)
            ],
            $options
        );

        return new User($this, $response['result']['_id'], $response['result']['_source'], $response['result']['_meta']);
    }

    /**
     * Replaces an existing user in Kuzzle.
     *
     * @param integer $id Unique user identifier, will be used as username
     * @param array $content Data representing the user
     * @param array $options Optional arguments
     *
     * @return User
     *
     * @throws InvalidArgumentException
     */
    public function replaceUser($id, array $content, array $options = [])
    {
        if (empty($id) || empty($content)) {
            throw new InvalidArgumentException('Kuzzle\Security::replaceUser: Unable to replace user: no id or content specified');
        }

        $options['httpParams'] = [':_id' => $id];

        $response = $this->kuzzle->query(
            $this->kuzzle->buildQueryArgs('security', 'replaceUser'),
            [
                '_id' => $id,
                'body' => json_encode($content)
            ],
            $options
        );

        return new User($this, $response['result']['_id'], $response['result']['_source'], $response['result']['_meta']);
    }

    /**
     * Delete profile.
     *
     * @param integer $id Unique profile identifier to delete
     * @param array $options Optional arguments
     *
     * @return integer Profile id which has been deleted.
     *
     * @throws InvalidArgumentException
     */
    public function deleteProfile($id, array $options = [])
    {
        if (empty($id)) {
            throw new InvalidArgumentException('Kuzzle\Security::deleteProfile: Unable to delete profile: no id specified');
        }

        $options['httpParams'] = [':_id' => $id];

        $response = $this->kuzzle->query(
            $this->kuzzle->buildQueryArgs('security', 'deleteProfile'),
            [
               '_id' => $id
            ],
            $options
        );

        return $response['result']['_id'];
    }

    /**
     * Returns the next profiles result set with scroll query.
     *
     * @param string $scrollId
     * @param array $options (optional) arguments
     *
     * @return ProfilesSearchResult
     *
     * @throws InvalidArgumentException
     */
    public function scrollProfiles($scrollId, array $options = [])
    {
        if (empty($scrollId)) {
            throw new InvalidArgumentException('Kuzzle\Security::scrollProfiles: scrollId is required');
        }

        $data = array();
        $options['httpParams'] = [':scrollId' => $scrollId];

        if (isset($options['scroll'])) {
            $data['scroll'] = $options['scroll'];
        }

        $response = $this->kuzzle->query(
            $this->kuzzle->buildQueryArgs('security', 'scrollProfiles'),
            $data,
            $options
        );

        $response['result']['hits'] = array_map(function ($document) {
            return new Profile($this, $document['_id'], $document['_source'], $document['_meta']);
        }, $response['result']['hits']);

        return new ProfilesSearchResult(
            $response['result']['total'],
            $response['result']['hits'],
            $scrollId
        );
    }

    /**
     * Delete role.
     *
     * @param integer $id Unique role identifier to delete
     * @param array $options Optional arguments
     *
     * @return integer Role id which has been deleted.
     *
     * @throws InvalidArgumentException
     */
    public function deleteRole($id, array $options = [])
    {
        if (empty($id)) {
            throw new InvalidArgumentException('Kuzzle\Security::deleteRole: id is required');
        }

        $options['httpParams'] = [':_id' => $id];

        $response = $this->kuzzle->query(
            $this->kuzzle->buildQueryArgs('security', 'deleteRole'),
            [
                '_id' => $id
            ],
            $options
        );

        return $response['result']['_id'];
    }

    /**
     * Delete user.
     *
     * @param integer $id Unique user identifier to delete
     * @param array $options Optional arguments
     *
     * @return integer User id which has been deleted.
     *
     * @throws InvalidArgumentException
     */
    public function deleteUser($id, array $options = [])
    {
        if (empty($id)) {
            throw new InvalidArgumentException('Kuzzle\Security::deleteUser: id is required');
        }

        $options['httpParams'] = [':_id' => $id];

        $response = $this->kuzzle->query(
            $this->kuzzle->buildQueryArgs('security', 'deleteUser'),
            [
                '_id' => $id
            ],
            $options
        );

        return $response['result']['_id'];
    }

    /**
     * Returns the next users result set with scroll query.
     *
     * @param string $scrollId
     * @param array $options (optional) arguments
     *
     * @return UsersSearchResult
     *
     * @throws InvalidArgumentException
     */
    public function scrollUsers($scrollId, array $options = [])
    {
        if (empty($scrollId)) {
            throw new InvalidArgumentException('Kuzzle\Security::scrollUsers: scrollId is required');
        }

        $data = [];
        $options['httpParams'] = [':scrollId' => $scrollId];

        if (isset($options['scroll'])) {
            $data['scroll'] = $options['scroll'];
        }

        $response = $this->kuzzle->query(
            $this->kuzzle->buildQueryArgs('security', 'scrollUsers'),
            $data,
            $options
        );

        $response['result']['hits'] = array_map(function ($document) {
            return new User($this, $document['_id'], $document['_source'], $document['_meta']);
        }, $response['result']['hits']);

        return new UsersSearchResult(
            $response['result']['total'],
            $response['result']['hits'],
            $scrollId
        );
    }

    /**
     * Retrieves a single stored profile using its unique ID.
     *
     * @param integer $id Unique profile identifier
     * @param array $options Optional arguments
     *
     * @return Profile
     *
     * @throws InvalidArgumentException
     */
    public function getProfile($id, array $options = [])
    {
        if (empty($id)) {
            throw new InvalidArgumentException('Kuzzle\Security::getProfile: id is required');
        }

        $options['httpParams'] = [':_id' => $id];

        $response = $this->kuzzle->query(
            $this->kuzzle->buildQueryArgs('security', 'getProfile'),
            [
                '_id' => $id
            ],
            $options
        );

        return new Profile($this, $response['result']['_id'], $response['result']['_source'], $response['result']['_meta']);
    }

    /**
     * Gets the mapping of the internal security profiles collection.
     *
     * @param array $options Optional arguments
     *
     * @return mixed
     *
     */
    public function getProfileMapping(array $options = [])
    {
        $response = $this->kuzzle->query(
            $this->kuzzle->buildQueryArgs('security', 'getProfileMapping'),
            [],
            $options
        );

        return $response['result']['mapping'];
    }


    /**
     * Retrieves a single stored role using its unique ID.
     *
     * @param integer $id Unique role identifier
     * @param array $options Optional arguments
     *
     * @return Role
     *
     * @throws InvalidArgumentException
     */
    public function getRole($id, array $options = [])
    {
        if (empty($id)) {
            throw new InvalidArgumentException('Kuzzle\Security::getRole: id is required');
        }

        $options['httpParams'] = [':_id' => $id];

        $response = $this->kuzzle->query(
            $this->kuzzle->buildQueryArgs('security', 'getRole'),
            [
                '_id' => $id
            ],
            $options
        );

        return new Role($this, $response['result']['_id'], $response['result']['_source'], $response['result']['_meta']);
    }

    /**
     * Gets the mapping of the internal security roles collection.
     *
     * @param array $options Optional arguments
     *
     * @return mixed
     *
     */
    public function getRoleMapping(array $options = [])
    {
        $response = $this->kuzzle->query(
            $this->kuzzle->buildQueryArgs('security', 'getRoleMapping'),
            [],
            $options
        );

        return $response['result']['mapping'];
    }

    /**
     * Retrieves a single stored user using its unique ID.
     *
     * @param integer $id Unique user identifier
     * @param array $options Optional arguments
     *
     * @return User
     *
     * @throws InvalidArgumentException
     */
    public function getUser($id, array $options = [])
    {
        if (empty($id)) {
            throw new InvalidArgumentException('Kuzzle\Security::getUser: id is required');
        }

        $options['httpParams'] = [':_id' => $id];

        $response = $this->kuzzle->query(
            $this->kuzzle->buildQueryArgs('security', 'getUser'),
            [
                '_id' => $id
            ],
            $options
        );

        return new User($this, $response['result']['_id'], $response['result']['_source'], $response['result']['_meta']);
    }

    /**
     * Gets the mapping of the internal security users collection.
     *
     * @param array $options Optional arguments
     *
     * @return mixed
     *
     */
    public function getUserMapping(array $options = [])
    {
        $response = $this->kuzzle->query(
            $this->kuzzle->buildQueryArgs('security', 'getUserMapping'),
            [],
            $options
        );

        return $response['result']['mapping'];
    }

    /**
     * Gets the rights of given user.
     *
     * @param integer $id Id of the user
     * @param array $options Optional arguments
     *
     * @return array
     *
     * @throws InvalidArgumentException
     */
    public function getUserRights($id, array $options = [])
    {
        if (empty($id)) {
            throw new InvalidArgumentException('Kuzzle\Security::getUserRights: id is required');
        }

        $options['httpParams'] = [':_id' => $id];

        $response = $this->kuzzle->query(
            $this->kuzzle->buildQueryArgs('security', 'getUserRights'),
            [
                '_id' => $id
            ],
            $options
        );

        return $response['result']['hits'];
    }

    /**
     * Gets the rights of given Profile.
     *
     * @param integer $id Id of the user
     * @param array $options Optional arguments
     *
     * @return array
     *
     * @throws InvalidArgumentException
     */
    public function getProfileRights($id, array $options = [])
    {
        if (empty($id)) {
            throw new InvalidArgumentException('Kuzzle\Security::getProfileRights: id is required');
        }

        $options['httpParams'] = [':_id' => $id];

        $response = $this->kuzzle->query(
            $this->kuzzle->buildQueryArgs('security', 'getProfileRights'),
            [
                '_id' => $id
            ],
            $options
        );

        return $response['result']['hits'];
    }

    /**
     * Executes a search on profiles according to a filter
     *
     * @param array $filters List of filters to retrieves profiles
     * @param array $options Optional arguments
     *
     * @return ProfilesSearchResult
     */
    public function searchProfiles(array $filters, array $options = [])
    {
        $scrollId = null;

        $response = $this->kuzzle->query(
            $this->kuzzle->buildQueryArgs('security', 'searchProfiles'),
            [
                'body' => json_encode($filters)
            ],
            $options
        );

        $response['result']['hits'] = array_map(function ($profile) {
            return new Profile($this, $profile['_id'], $profile['_source'], $profile['_meta']);
        }, $response['result']['hits']);

        if (isset($response['result']['scrollId'])) {
            $scrollId = $response['result']['scrollId'];
        }

        return new ProfilesSearchResult($response['result']['total'], $response['result']['hits'], $scrollId);
    }

    /**
     * Executes a search on roles according to a filter
     *
     * @param array $filters List of filters to retrieves roles
     * @param array $options Optional arguments
     *
     * @return RolesSearchResult
     */
    public function searchRoles(array $filters, array $options = [])
    {
        $response = $this->kuzzle->query(
            $this->kuzzle->buildQueryArgs('security', 'searchRoles'),
            [
                'body' => json_encode($filters)
            ],
            $options
        );

        $response['result']['hits'] = array_map(function ($role) {
            return new Role($this, $role['_id'], $role['_source'], $role['_meta']);
        }, $response['result']['hits']);

        return new RolesSearchResult($response['result']['total'], $response['result']['hits']);
    }

    /**
     * Executes a search on users according to a filter
     *
     * @param array $filters List of filters to retrieves users
     * @param array $options Optional arguments
     *
     * @return UsersSearchResult
     */
    public function searchUsers(array $filters, array $options = [])
    {
        $scrollId = null;

        $response = $this->kuzzle->query(
            $this->kuzzle->buildQueryArgs('security', 'searchUsers'),
            [
                'body' => json_encode($filters)
            ],
            $options
        );

        $response['result']['hits'] = array_map(function ($user) {
            return new User($this, $user['_id'], $user['_source'], $user['_meta']);
        }, $response['result']['hits']);

        if (isset($response['result']['scrollId'])) {
            $scrollId = $response['result']['scrollId'];
        }

        return new UsersSearchResult($response['result']['total'], $response['result']['hits'], $scrollId);
    }

    /**
     * Performs a partial update on an existing profile.
     *
     * @param string $id Unique profile identifier
     * @param array $policies List of policies to apply to this profile
     * @param array $options Optional arguments
     *
     * @return Profile
     *
     * @throws InvalidArgumentException
     */
    public function updateProfile($id, array $policies, array $options = [])
    {
        if (empty($id) || empty($policies)) {
            throw new InvalidArgumentException('Kuzzle\Security::updateProfile: id and policies are required');
        }

        $options['httpParams'] = [':_id' => $id];

        $response = $this->kuzzle->query(
            $this->kuzzle->buildQueryArgs('security', 'updateProfile'),
            [
                '_id' => $id,
                'body' => json_encode([ 'policies' => $policies ])
            ],
            $options
        );

        return new Profile($this, $id, $response['result']['_source'], $response['result']['_meta']);
    }

    /**
     * Performs an update on the internal profiles collection mapping.
     *
     * @param array $mapping New mapping
     * @param array $options Optional arguments
     *
     * @return bool
     *
     * @throws InvalidArgumentException
     */
    public function updateProfileMapping(array $mapping = [], array $options = [])
    {
        if (empty($mapping)) {
            throw new InvalidArgumentException('Kuzzle\Security::updateProfileMapping: mapping is required');
        }

        $response = $this->kuzzle->query(
            $this->kuzzle->buildQueryArgs('security', 'updateProfileMapping'),
            [
                'body' => json_encode([ 'properties' => $mapping ])
            ],
            $options
        );

        return $response['result']['acknowledged'];
    }

    /**
     * Performs a partial update on an existing role.
     *
     * @param string $id Unique role identifier
     * @param array $content Data representing the role
     * @param array $options Optional arguments
     *
     * @return Role
     *
     * @throws InvalidArgumentException
     */
    public function updateRole($id, array $content, array $options = [])
    {
        if (empty($id) || empty($content)) {
            throw new InvalidArgumentException('Kuzzle\Security::updateRole: id and content are required');
        }

        $options['httpParams'] = [':_id' => $id];

        $response = $this->kuzzle->query(
            $this->kuzzle->buildQueryArgs('security', 'updateRole'),
            [
                '_id' => $id,
                'body' => json_encode($content)
            ],
            $options
        );

        return new Role($this, $id, $response['result']['_source'], $response['result']['_meta']);
    }

    /**
     * Performs an update on the internal roles collection mapping.
     *
     * @param array $mapping New mapping
     * @param array $options Optional arguments
     *
     * @return bool
     *
     * @throws InvalidArgumentException
     */
    public function updateRoleMapping(array $mapping = [], array $options = [])
    {
        if (empty($mapping)) {
            throw new InvalidArgumentException('Kuzzle\Security::updateRoleMapping: mapping is required');
        }

        $response = $this->kuzzle->query(
            $this->kuzzle->buildQueryArgs('security', 'updateRoleMapping'),
            [
                'body' => json_encode([ 'properties' => $mapping ])
            ],
            $options
        );

        return $response['result']['acknowledged'];
    }

    /**
     * Performs a partial update on an existing user.
     *
     * @param string $id Unique user identifier
     * @param array $content Data representing the user
     * @param array $options Optional arguments
     *
     * @return User
     *
     * @throws InvalidArgumentException
     */
    public function updateUser($id, array $content, array $options = [])
    {
        if (empty($id) || empty($content)) {
            throw new InvalidArgumentException('Kuzzle\Security::updateUser: id and content are required');
        }

        $options['httpParams'] = [':_id' => $id];

        $response = $this->kuzzle->query(
            $this->kuzzle->buildQueryArgs('security', 'updateUser'),
            [
                '_id' => $id,
                'body' => json_encode($content)
            ],
            $options
        );

        return new User($this, $id, $response['result']['_source'], $response['result']['_meta']);
    }

    /**
     * Performs an update on the internal users collection mapping.
     *
     * @param array $mapping New mapping
     * @param array $options Optional arguments
     *
     * @return bool
     *
     * @throws InvalidArgumentException
     */
    public function updateUserMapping(array $mapping = [], array $options = [])
    {
        if (empty($mapping)) {
            throw new InvalidArgumentException('Kuzzle\Security::updateUserMapping: mapping is required');
        }

        $response = $this->kuzzle->query(
            $this->kuzzle->buildQueryArgs('security', 'updateUserMapping'),
            [
                'body' => json_encode([ 'properties' => $mapping ])
            ],
            $options
        );

        return $response['result']['acknowledged'];
    }

    /**
     * Create credentials of the specified <strategy> for the user <kuid>.
     *
     * @param $strategy
     * @param $kuid
     * @param $credentials
     * @param array $options
     *
     * @return mixed
     *
     * @throws InvalidArgumentException
     */
    public function createCredentials($strategy, $kuid, $credentials, array $options = [])
    {
        if (empty($strategy) || empty($kuid) || empty($credentials)) {
            throw new InvalidArgumentException('Kuzzle\Security::createCredentials: strategy, kuid and credentials are required');
        }

        $options['httpParams'] = [
            ':strategy' => $strategy,
            ':kuid' => $kuid
        ];

        return $this->kuzzle->query(
            $this->kuzzle->buildQueryArgs('security', 'createCredentials'),
            [
                'strategy' => $strategy,
                '_id' => $kuid,
                'body' => json_encode($credentials)
            ],
            $options
        )['result'];
    }

    /**
     * Delete credentials of the specified <strategy> for the user <kuid> .
     *
     * @param $strategy
     * @param $kuid
     * @param array $options
     *
     * @return mixed
     *
     * @throws InvalidArgumentException
     */
    public function deleteCredentials($strategy, $kuid, array $options = [])
    {
        if (empty($strategy) || empty($kuid)) {
            throw new InvalidArgumentException('Kuzzle\Security::deleteCredentials: strategy and kuid are required');
        }

        $options['httpParams'] = [
            ':strategy' => $strategy,
            ':kuid' => $kuid
        ];

        return $this->kuzzle->query(
            $this->kuzzle->buildQueryArgs('security', 'deleteCredentials'),
            [
                'strategy' => $strategy,
                '_id' => $kuid,
            ],
            $options
        )['result'];
    }

    /**
     * Retrieve a list of accepted fields per authentication strategy.
     *
     * @param array $options
     * @return mixed
     */
    public function getAllCredentialFields(array $options = [])
    {
        return $this->kuzzle->query(
            $this->kuzzle->buildQueryArgs('security', 'getAllCredentialFields'),
            [],
            $options
        )['result'];
    }

    /**
     * Retrieve the list of accepted field names by the specified <strategy>.
     *
     * @param $strategy
     * @param array $options
     *
     * @return mixed
     *
     * @throws InvalidArgumentException
     */
    public function getCredentialFields($strategy, array $options = [])
    {
        if (empty($strategy)) {
            throw new InvalidArgumentException('Kuzzle\Security::getCredentialFields: strategy is required');
        }

        $options['httpParams']= [':strategy' => $strategy];

        return $this->kuzzle->query(
            $this->kuzzle->buildQueryArgs('security', 'getCredentialFields'),
            [
                'strategy' => $strategy
            ],
            $options
        )['result'];
    }

    /**
     * Get credential information of the specified <strategy> for the user <kuid>.
     *
     * @param $strategy
     * @param $kuid
     * @param array $options
     *
     * @return mixed
     *
     * @throws InvalidArgumentException
     */
    public function getCredentials($strategy, $kuid, array $options = [])
    {
        if (empty($strategy) || empty($kuid)) {
            throw new InvalidArgumentException('Kuzzle\Security::getCredentials: strategy and kuid are required');
        }

        $options['httpParams'] = [
            ':strategy' => $strategy,
            ':kuid' => $kuid
        ];

        return $this->kuzzle->query(
            $this->kuzzle->buildQueryArgs('security', 'getCredentials'),
            [
                'strategy' => $strategy,
                '_id' => $kuid
            ],
            $options
        )['result'];
    }

    /**
     * Get credential information of the specified <strategyId> (storage key of the strategy) of the user.
     *
     * @param $strategy
     * @param $kuid
     * @param array $options
     *
     * @return mixed
     *
     * @throws InvalidArgumentException
     */
    public function getCredentialsById($strategy, $kuid, array $options = [])
    {
        if (empty($strategy) || empty($kuid)) {
            throw new InvalidArgumentException('Kuzzle\Security::getCredentialsById: strategy and kuid are required');
        }

        $options['httpParams'] = [
            ':strategy' => $strategy,
            ':kuid' => $kuid
        ];

        return $this->kuzzle->query(
            $this->kuzzle->buildQueryArgs('security', 'getCredentialsById'),
            [
                'strategy' => $strategy,
                '_id' => $kuid
            ],
            $options
        )['result'];
    }

    /**
     * Check the existence of the specified <strategy>'s credentials for the user <kuid>.
     *
     * @param $strategy
     * @param $kuid
     * @param array $options
     *
     * @return mixed
     *
     * @throws InvalidArgumentException
     */
    public function hasCredentials($strategy, $kuid, array $options = [])
    {
        if (empty($strategy) || empty($kuid)) {
            throw new InvalidArgumentException('Kuzzle\Security::hasCredentials: strategy and kuid are required');
        }

        $options['httpParams'] = [
            ':strategy' => $strategy,
            ':kuid' => $kuid
        ];

        return $this->kuzzle->query(
            $this->kuzzle->buildQueryArgs('security', 'hasCredentials'),
            [
                'strategy' => $strategy,
                '_id' => $kuid
            ],
            $options
        )['result'];
    }

    /**
     * Updates credentials of the specified <strategy> for the user <kuid>.
     *
     * @param $strategy
     * @param $kuid
     * @param $credentials
     * @param array $options
     *
     * @return mixed
     *
     * @throws InvalidArgumentException
     */
    public function updateCredentials($strategy, $kuid, $credentials, array $options = [])
    {
        if (empty($strategy) || empty($kuid) || empty($credentials)) {
            throw new InvalidArgumentException('Kuzzle\Security::updateCredentials: strategy, kuid and credentials are required');
        }

        $options['httpParams'] = [
            ':strategy' => $strategy,
            ':kuid' => $kuid
        ];

        return $this->kuzzle->query(
            $this->kuzzle->buildQueryArgs('security', 'updateCredentials'),
            [
                'strategy' => $strategy,
                '_id' => $kuid,
                "body" => json_encode($credentials)
            ],
            $options
        )['result'];
    }

    /**
     * Validate credentials of the specified <strategy> for the user <kuid>.
     *
     * @param $strategy
     * @param $kuid
     * @param $credentials
     * @param array $options
     *
     * @return mixed
     *
     * @throws InvalidArgumentException
     */
    public function validateCredentials($strategy, $kuid, $credentials, array $options = [])
    {
        if (empty($strategy) || empty($kuid) || empty($credentials)) {
            throw new InvalidArgumentException('Kuzzle\Security::validateCredentials: strategy, kuid and credentials are required');
        }

        $options['httpParams'] = [
            ':strategy' => $strategy,
            ':kuid' => $kuid
        ];

        return $this->kuzzle->query(
            $this->kuzzle->buildQueryArgs('security', 'validateCredentials'),
            [
                'strategy' => $strategy,
                '_id' => $kuid,
                "body" => json_encode($credentials)
            ],
            $options
        )['result'];
    }

    /**
     * Get multiple profiles.
     *
     * @param array $ids array of profile ids
     * @param array $options Optional arguments
     *
     * @return array Profile which have been fetched.
     *
     * @throws InvalidArgumentException
     */
    public function mGetProfiles(array $ids, array $options = [])
    {
        if (empty($ids)) {
            throw new InvalidArgumentException('Kuzzle\Security::mGetProfiles: Unable to get profiles: no ids specified');
        }

        $response = $this->kuzzle->query(
            $this->kuzzle->buildQueryArgs('security', 'mGetProfiles'),
            [
               'body' => json_encode(['ids' => $ids])
            ],
            $options
        );

        return $response['result']['hits'];
    }

    /**
     * Get multiple roles.
     *
     * @param array $ids array of role ids
     * @param array $options Optional arguments
     *
     * @return array Roles which have been fetched.
     *
     * @throws InvalidArgumentException
     */
    public function mGetRoles(array $ids, array $options = [])
    {
        if (empty($ids)) {
            throw new InvalidArgumentException('Kuzzle\Security::mGetRoles: Unable to get roles: no ids specified');
        }

        $response = $this->kuzzle->query(
            $this->kuzzle->buildQueryArgs('security', 'mGetRoles'),
            [
               'body' => json_encode(['ids' => $ids])
            ],
            $options
        );

        return $response['result']['hits'];
    }

    /**
     * Delete multiple profiles.
     *
     * @param array $ids array of profile ids
     * @param array $options Optional arguments
     *
     * @return array Profile ids which have been deleted.
     *
     * @throws InvalidArgumentException
     */
    public function mDeleteProfiles(array $ids, array $options = [])
    {
        if (empty($ids)) {
            throw new InvalidArgumentException('Kuzzle\Security::mDeleteProfiles: Unable to delete profiles: no ids specified');
        }

        $response = $this->kuzzle->query(
            $this->kuzzle->buildQueryArgs('security', 'mDeleteProfiles'),
            [
               'body' => json_encode(['ids' => $ids])
            ],
            $options
        );

        return $response['result'];
    }

    /**
     * Delete multiple roles.
     *
     * @param array $ids array of role ids
     * @param array $options Optional arguments
     *
     * @return array Roles ids which have been deleted.
     *
     * @throws InvalidArgumentException
     */
    public function mDeleteRoles(array $ids, array $options = [])
    {
        if (empty($ids)) {
            throw new InvalidArgumentException('Kuzzle\Security::mDeleteRoles: Unable to delete roles: no ids specified');
        }

        $response = $this->kuzzle->query(
            $this->kuzzle->buildQueryArgs('security', 'mDeleteRoles'),
            [
               'body' => json_encode(['ids' => $ids])
            ],
            $options
        );

        return $response['result'];
    }

    /**
     * Delete multiple users.
     *
     * @param array $ids array of users ids
     * @param array $options Optional arguments
     *
     * @return array Users ids which have been deleted.
     *
     * @throws InvalidArgumentException
     */
    public function mDeleteUsers(array $ids, array $options = [])
    {
        if (empty($ids)) {
            throw new InvalidArgumentException('Kuzzle\Security::mDeleteUsers: Unable to delete users: no ids specified');
        }

        $response = $this->kuzzle->query(
            $this->kuzzle->buildQueryArgs('security', 'mDeleteUsers'),
            [
               'body' => json_encode(['ids' => $ids])
            ],
            $options
        );

        return $response['result'];
    }
}
