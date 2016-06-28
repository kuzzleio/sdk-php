<?php

namespace Kuzzle\Security;

/**
 * Class User
 * @package kuzzleio/kuzzle-sdk
 */
class User extends Document
{
    protected $deleteActionName = 'deleteUser';

    protected $updateActionName = 'createOrReplaceUser';

    protected $saveActionName = 'updateUser';

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

        if (!array_key_exists('profile', $this->content)) {
            $this->content['profile'] = User::DEFAULT_PROFILE;
        }

        /*
         * Remove profile data to keep only it's id
         * @todo: refactor this at repository refactor
         */
        $this->content['profile'] = $this->extractProfileId($this->content['profile']);


        return $this;
    }

    /**
     * Returns this user associated profile.
     *
     * @return Profile
     */
    public function getProfile()
    {
        return $this->security->getProfile($this->content['profile']);
    }

    /**
     * Replaces the profile associated to this user.
     *
     * @param string|Profile $profile Unique id or Kuzzle\Security\Role instance corresponding to the new associated role
     * @return User
     */
    public function setProfile($profile)
    {
        /*
         * @todo: refactor this at repository refactor
         */
        $this->content['profile'] = $this->extractProfileId($profile);

        return $this;
    }


    /**
     * @param Profile|array|string $profile
     * @return string
     * @todo: refactor this at repository refactor
     */
    protected function extractProfileId($profile)
    {
        if (is_array($profile) && array_key_exists('_id', $profile)) {
            $profile = $profile['_id'];
        }

        if ($profile instanceof Profile) {
            $profile = $profile->getId();
        }

        return $profile;
    }
}
