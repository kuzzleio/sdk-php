<?php

namespace Kuzzle\Security;

use ErrorException;
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
     * @var string action is authorized without condition
     */
    const ACTION_ALLOWED = 'allowed';

    /**
     * @var string the authorization depends on a closure
     */
    const ACTION_DENIED = 'denied';

    /**
     * @var string action is forbidden
     */
    const ACTION_CONDITIONAL = 'conditional';


    /**
     * @var Kuzzle
     */
    private $kuzzle;


    /**
     * Security constructor.
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
     * @param integer $id Unique profile identifier
     * @param array $content Data representing the profile
     * @param array $options Optional arguments
     * @return Profile
     */
    public function createProfile($id, array $content, array $options = [])
    {
        $action = 'createProfile';
        $data = [
            '_id' => $id,
            'body' => $content
        ];

        if (array_key_exists('replaceIfExist', $options)) {
            $action = 'createOrReplaceProfile';
        }

        $response = $this->kuzzle->query(
            $this->buildQueryArgs($action),
            $data,
            $options
        );

        return new Profile($this, $response['result']['_id'], $response['result']['_source']);
    }

    /**
     * Create a new role in Kuzzle.
     *
     * @param integer $id Unique role identifier
     * @param array $content Data representing the role
     * @param array $options Optional arguments
     * @return Role
     */
    public function createRole($id, array $content, array $options = [])
    {
        $action = 'createRole';
        $data = [
            '_id' => $id,
            'body' => $content
        ];

        if (array_key_exists('replaceIfExist', $options)) {
            $action = 'createOrReplaceRole';
        }

        $response = $this->kuzzle->query(
            $this->buildQueryArgs($action),
            $data,
            $options
        );

        return new Role($this, $response['result']['_id'], $response['result']['_source']);
    }

    /**
     * Create a new user in Kuzzle.
     *
     * @param integer $id Unique user identifier, will be used as username
     * @param array $content Data representing the user
     * @param array $options Optional arguments
     * @return User
     */
    public function createUser($id, array $content, array $options = [])
    {
        $action = 'createUser';
        $data = [
            '_id' => $id,
            'body' => $content
        ];

        if (array_key_exists('replaceIfExist', $options)) {
            $action = 'createOrReplaceUser';
        }

        $response = $this->kuzzle->query(
            $this->buildQueryArgs($action),
            $data,
            $options
        );

        return new User($this, $response['result']['_id'], $response['result']['_source']);
    }

    /**
     * Create a new restricted user in Kuzzle.
     *
     * @param integer $id Unique user identifier, will be used as username
     * @param array $content Data representing the user
     * @param array $options Optional arguments
     * @return User
     */
    public function createRestrictedUser($id, array $content, array $options = [])
    {
        $action = 'createRestrictedUser';
        $data = [
            '_id' => $id,
            'body' => $content
        ];

        $response = $this->kuzzle->query(
            $this->buildQueryArgs($action),
            $data,
            $options
        );

        return new User($this, $response['result']['_id'], $response['result']['_source']);
    }

    /**
     * Delete profile.
     *
     * @param integer $id Unique profile identifier to delete
     * @param array $options Optional arguments
     * @return integer Profile id which has been deleted.
     */
    public function deleteProfile($id, array $options = [])
    {
        $data = [
            '_id' => $id
        ];

        $response = $this->kuzzle->query(
            $this->buildQueryArgs('deleteProfile'),
            $data,
            $options
        );

        return $response['result']['_id'];
    }

    /**
     * Delete role.
     *
     * @param integer $id Unique role identifier to delete
     * @param array $options Optional arguments
     * @return integer Role id which has been deleted.
     */
    public function deleteRole($id, array $options = [])
    {
        $data = [
            '_id' => $id
        ];

        $response = $this->kuzzle->query(
            $this->buildQueryArgs('deleteRole'),
            $data,
            $options
        );

        return $response['result']['_id'];
    }

    /**
     * Delete user.
     *
     * @param integer $id Unique user identifier to delete
     * @param array $options Optional arguments
     * @return integer User id which has been deleted.
     */
    public function deleteUser($id, array $options = [])
    {
        $data = [
            '_id' => $id
        ];

        $response = $this->kuzzle->query(
            $this->buildQueryArgs('deleteUser'),
            $data,
            $options
        );

        return $response['result']['_id'];
    }

    /**
     * Retrieves a single stored profile using its unique ID.
     *
     * @param integer $id Unique profile identifier
     * @param array $options Optional arguments
     * @return Profile
     */
    public function getProfile($id, array $options = [])
    {
        $data = [
            '_id' => $id
        ];

        $response = $this->kuzzle->query(
            $this->buildQueryArgs('getProfile'),
            $data,
            $options
        );

        return new Profile($this, $response['result']['_id'], $response['result']['_source']);
    }

    /**
     * Retrieves a single stored role using its unique ID.
     *
     * @param integer $id Unique role identifier
     * @param array $options Optional arguments
     * @return Role
     */
    public function getRole($id, array $options = [])
    {
        $data = [
            '_id' => $id
        ];

        $response = $this->kuzzle->query(
            $this->buildQueryArgs('getRole'),
            $data,
            $options
        );

        return new Role($this, $response['result']['_id'], $response['result']['_source']);
    }

    /**
     * Retrieves a single stored user using its unique ID.
     *
     * @param integer $id Unique user identifier
     * @param array $options Optional arguments
     * @return User
     */
    public function getUser($id, array $options = [])
    {
        $data = [
            '_id' => $id
        ];

        $response = $this->kuzzle->query(
            $this->buildQueryArgs('getUser'),
            $data,
            $options
        );

        return new User($this, $response['result']['_id'], $response['result']['_source']);
    }

    /**
     * Gets the rights of given user.
     *
     * @param integer $id Id of the user
     * @param array $options Optional arguments
     * @return array
     */
    public function getUserRights($id, array $options = [])
    {
        $data = [
            '_id' => $id
        ];

        $response = $this->kuzzle->query(
            $this->buildQueryArgs('getUserRights'),
            $data,
            $options
        );

        return $response['result']['hits'];
    }

    /**
     * Tells whether an action is allowed, denied or conditional based on the rights provided as the first argument
     *
     * @param array $rights Rights list (@see Security::getUserRights)
     * @param string $controller The controller
     * @param string $action The action
     * @param string $index Optional index
     * @param string $collection Optional collection
     * @return string
     *  Security::ACTION_ALLOWED
     *  Security::ACTION_DENIED
     *  Security::ACTION_CONDITIONAL
     *
     * @throws ErrorException
     */
    public function isActionAllowed(array $rights, $controller, $action, $index = '', $collection = '')
    {
        // We filter in all the rights that match the request (including wildcards).
        $filteredRights = array_filter($rights, function (array $right) use ($controller) {
            return array_key_exists('controller', $right) && ($right['controller'] === $controller || $right['controller'] === '*');
        });

        $filteredRights = array_filter($filteredRights, function (array $right) use ($action) {
            return array_key_exists('action', $right) && ($right['action'] === $action || $right['action'] === '*');
        });

        $filteredRights = array_filter($filteredRights, function (array $right) use ($index) {
            return array_key_exists('index', $right) && ($right['index'] === $index || $right['index'] === '*');
        });

        $filteredRights = array_filter($filteredRights, function (array $right) use ($collection) {
            return array_key_exists('collection', $right) && ($right['collection'] === $collection || $right['collection'] === '*');
        });

        $rightsValues = array_map(function ($element) {
            return $element['value'];
        }, $filteredRights);

        // Then, if at least one right allows the action, we return Security::ACTION_ALLOWED
        if (array_search(Security::ACTION_ALLOWED, $rightsValues) !== false) {
            return Security::ACTION_ALLOWED;
        } // If no right allows the action, we check for Security::ACTION_CONDITIONAL.
        elseif (array_search(Security::ACTION_CONDITIONAL, $rightsValues) !== false) {
            return Security::ACTION_CONDITIONAL;
        }

        // Otherwise we return Security::ACTION_DENIED.
        return Security::ACTION_DENIED;
    }

    /**
     * Instantiate a new Kuzzle\Security\Profile object.
     *
     * @param string $id Unique profile identifier
     * @param array $content Profile content
     * @return Profile
     */
    public function profileFactory($id, array $content)
    {
        return new Profile($this, $id, $content);
    }

    /**
     * Instantiate a new Kuzzle\Security\Role object.
     *
     * @param string $id Unique role identifier
     * @param array $content Role content
     * @return Role
     */
    public function roleFactory($id, array $content)
    {
        return new Role($this, $id, $content);
    }

    /**
     * Instantiate a new Kuzzle\Security\User object.
     *
     * @param string $id Unique user identifier
     * @param array $content User content
     * @return User
     */
    public function userFactory($id, array $content)
    {
        return new User($this, $id, $content);
    }

    /**
     * Executes a search on profiles according to a filter
     *
     * @param array $filters List of filters to retrieves profiles
     * @param array $options Optional arguments
     * @return ProfilesSearchResult
     */
    public function searchProfiles(array $filters, array $options = [])
    {
        $data = [
            'body' => $filters
        ];

        $response = $this->kuzzle->query(
            $this->buildQueryArgs('searchProfiles'),
            $data,
            $options
        );

        $response['result']['hits'] = array_map(function ($profile) {
            return new Profile($this, $profile['_id'], $profile['_source']);
        }, $response['result']['hits']);

        return new ProfilesSearchResult($response['result']['total'], $response['result']['hits']);
    }

    /**
     * Executes a search on roles according to a filter
     *
     * @param array $filters List of filters to retrieves roles
     * @param array $options Optional arguments
     * @return RolesSearchResult
     */
    public function searchRoles(array $filters, array $options = [])
    {
        $data = [
            'body' => $filters
        ];

        $response = $this->kuzzle->query(
            $this->buildQueryArgs('searchRoles'),
            $data,
            $options
        );

        $response['result']['hits'] = array_map(function ($role) {
            return new Role($this, $role['_id'], $role['_source']);
        }, $response['result']['hits']);

        return new RolesSearchResult($response['result']['total'], $response['result']['hits']);
    }

    /**
     * Executes a search on users according to a filter
     *
     * @param array $filters List of filters to retrieves users
     * @param array $options Optional arguments
     * @return UsersSearchResult
     */
    public function searchUsers(array $filters, array $options = [])
    {
        $data = [
            'body' => $filters
        ];

        $response = $this->kuzzle->query(
            $this->buildQueryArgs('searchUsers'),
            $data,
            $options
        );

        $response['result']['hits'] = array_map(function ($user) {
            return new User($this, $user['_id'], $user['_source']);
        }, $response['result']['hits']);

        return new UsersSearchResult($response['result']['total'], $response['result']['hits']);
    }

    /**
     * Performs a partial update on an existing profile.
     *
     * @param string $id Unique profile identifier
     * @param array $content Data representing the profile
     * @param array $options Optional arguments
     * @return Profile
     */
    public function updateProfile($id, array $content, array $options = [])
    {
        $data = [
            '_id' => $id,
            'body' => $content
        ];

        $response = $this->kuzzle->query(
            $this->buildQueryArgs('updateProfile'),
            $data,
            $options
        );

        return new Profile($this, $id, $response['result']['_source']);
    }

    /**
     * Performs a partial update on an existing role.
     *
     * @param string $id Unique role identifier
     * @param array $content Data representing the role
     * @param array $options Optional arguments
     * @return Role
     */
    public function updateRole($id, array $content, array $options = [])
    {
        $data = [
            '_id' => $id,
            'body' => $content
        ];

        $response = $this->kuzzle->query(
            $this->buildQueryArgs('updateRole'),
            $data,
            $options
        );

        return new Role($this, $id, $response['result']['_source']);
    }

    /**
     * Performs a partial update on an existing user.
     *
     * @param string $id Unique user identifier
     * @param array $content Data representing the user
     * @param array $options Optional arguments
     * @return User
     */
    public function updateUser($id, array $content, array $options = [])
    {
        $data = [
            '_id' => $id,
            'body' => $content
        ];

        $response = $this->kuzzle->query(
            $this->buildQueryArgs('updateUser'),
            $data,
            $options
        );

        return new User($this, $id, $response['result']['_source']);
    }

    /**
     * @return Kuzzle
     */
    public function getKuzzle()
    {
        return $this->kuzzle;
    }

    /**
     * @param $action
     * @return array
     */
    public function buildQueryArgs($action)
    {
        return $this->kuzzle->buildQueryArgs('security', $action);
    }
}
