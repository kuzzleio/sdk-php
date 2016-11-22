<?php

namespace Kuzzle\Security;

/**
 * Class User
 * @package kuzzleio/kuzzle-sdk
 */
class User extends Document
{
    protected $deleteActionName = 'deleteUser';

    protected $updateActionName = 'updateUser';

    protected $saveActionName = 'createOrReplaceUser';

    const DEFAULT_PROFILE = 'default';

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
            $profiles[] = $this->security->getProfile($profileId);
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
}
