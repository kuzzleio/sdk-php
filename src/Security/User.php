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
     * @return Profile[]
     */
    public function getProfiles()
    {
        $profiles = [];

        foreach ($this->content['profilesIds'] as $profileId) {
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
        $profilesIds = [];

        foreach ($profiles as $profile) {
            $profilesIds[] = $this->extractProfileId($profile);
        }

        $this->content['profilesIds'] = $profilesIds;

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
        $this->content['profilesIds'][] = $this->extractProfileId($profile);

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
        if (!array_key_exists('profilesIds', $this->content)) {
            $this->content['profilesIds'] = [User::DEFAULT_PROFILE];
        }

        $profilesIds = [];

        foreach ($this->content['profilesIds'] as $profile) {
            $profilesIds[] = $this->extractProfileId($profile);
        }

        $this->content['profilesIds'] = $profilesIds;
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
