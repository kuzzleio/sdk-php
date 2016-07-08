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
     * @return Profile
     */
    public function getProfile()
    {
        return $this->security->getProfile($this->content['profileId']);
    }

    /**
     * Replaces the profile associated to this user.
     *
     * @param string|Profile $profile Unique id or Kuzzle\Security\Profile instance corresponding to the new associated profile
     * @return User
     */
    public function setProfile($profile)
    {
        $this->content['profileId'] = $this->extractProfileId($profile);

        return $this;
    }

    /**
     * Replaces the content of the Kuzzle\Security\Profile object.
     *
     * @param array $content
     * @return Profile
     */
    public function setContent(array $content)
    {
        parent::setContent($content);

        $this->syncProfile();

        return $this;
    }

    protected function syncProfile()
    {
        if (!array_key_exists('profileId', $this->content)) {
            $this->content['profileId'] = User::DEFAULT_PROFILE;
        }

        $this->content['profileId'] = $this->extractProfileId($this->content['profileId']);
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
