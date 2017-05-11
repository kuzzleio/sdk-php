<?php

namespace Kuzzle\Security;

use BadMethodCallException;

/**
 * Class User
 * @package kuzzleio/kuzzle-sdk
 */
class User extends Document
{
    protected $deleteActionName = 'deleteUser';

    protected $updateActionName = 'updateUser';

    protected $saveActionName = 'createUser';

    const DEFAULT_PROFILE = 'default';

    protected $credentials = [];

    /**
     * Role constructor.
     *
     * @param Security $kuzzleSecurity An instantiated Kuzzle\Security object
     * @param string $id Unique user identifier
     * @param array $content User content
     * @return User
     */
    public function __construct(Security $kuzzleSecurity, $id = '', array $content = [])
    {
        parent::__construct($kuzzleSecurity, $id, $content);

        $this->syncProfile();

        return $this;
    }

    /**
     * Returns this user associated profile.
     *
     * @return Profile[]|false
     */
    public function getProfiles()
    {
        if (!array_key_exists('profileIds', $this->content)) {
            return [];
        }

        $profiles = [];

        foreach ($this->content['profileIds'] as $profileId) {
            $profiles[] = $this->security->fetchProfile($profileId);
        }

        return $profiles;
    }

    /**
     * Replaces the profiles associated to this user.
     *
     * @param string[]|Profile[] $profiles Unique ids or Kuzzle\Security\Profile instances corresponding to the new associated profiles
     * @return $this
     */
    public function setProfiles($profiles)
    {
        $profileIds = [];

        foreach ($profiles as $profile) {
            $profileIds[] = $this->extractProfileId($profile);
        }

        $this->content['profileIds'] = $profileIds;

        return $this;
    }

    /**
     * Add a profile to this user.
     *
     * @param string|Profile $profile Unique id or Kuzzle\Security\Profile instances corresponding to the new associated profile
     * @return $this
     */
    public function addProfile($profile)
    {
        if (!array_key_exists('profileIds', $this->content)) {
            $this->content['profileIds'] = [];
        }

        $this->content['profileIds'][] = $this->extractProfileId($profile);

        return $this;
    }

    /**
     * Replaces the content of the Kuzzle\Security\Profile object.
     *
     * @param array $content
     * @return User
     */
    public function setContent(array $content)
    {
        parent::setContent($content);

        $this->syncProfile();

        return $this;
    }

    /**
     * @return bool
     */
    protected function syncProfile()
    {
        if (!array_key_exists('profileIds', $this->content)) {
            return false;
        }

        $profileIds = [];

        foreach ($this->content['profileIds'] as $profile) {
            $profileIds[] = $this->extractProfileId($profile);
        }

        $this->content['profileIds'] = $profileIds;

        return true;
    }

    /**
     * @param Profile|array|string $profile
     * @return string
     */
    protected function extractProfileId($profile)
    {
        if ($profile instanceof Profile) {
            return $profile->getId();
        }

        return $profile;
    }

    /**
     * Throws an error as this method can't be implemented for User
     *
     * @param array $options Optional parameters
     * @return void
     * @throws BadMethodCallException
     */
    public function save(array $options = [])
    {
        throw new BadMethodCallException('This method does not exist in User');
    }


    /**
     * Performs a partial content update on this object.
     *
     * @param array $content New profile content
     * @param array $options Optional parameters
     * @return Document
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
     * Creates the user in Kuzzle’s database layer.
     *
     * @param array $options Optional parameters
     * @return Document
     */
    public function create(array $options = [])
    {
        $data = $this->creationSerialize();

        $this->security->getKuzzle()->query(
            $this->security->buildQueryArgs('createUser'),
            $data,
            $options
        );

        return $this;
    }

    /**
     * Replaces the user in Kuzzle’s database layer.
     *
     * @param array $options Optional parameters
     * @return Document
     */
    public function replace(array $options = [])
    {
        $data = $this->serialize();

        $this->security->getKuzzle()->query(
            $this->security->buildQueryArgs('replaceUser'),
            $data,
            $options
        );

        return $this;
    }

    /**
     * @return array
     */
    private function creationSerialize()
    {
        $data = [];

        if (!empty($this->id)) {
            $data['_id'] = $this->id;
        }

        $data['body'] = [
            'content' => $this->content,
            'credentials' => $this->credentials
        ];


        return $data;
    }

    /**
     * @param array $credentials
     * @throws \InvalidArgumentException
     */
    public function setCredentials($credentials)
    {
        if (!is_array($credentials)) {
            throw new \InvalidArgumentException('Parameter "credentials" must be a object');
        }

        $this->credentials = $credentials;
    }

}
