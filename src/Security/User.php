<?php

namespace Kuzzle\Security;

use BadMethodCallException;
use Kuzzle\Kuzzle;

/**
 * Class User
 * @package kuzzleio/kuzzle-sdk
 */
class User
{
    /**
     * @var Kuzzle The kuzzle security instance associated to this User
     */
    public $kuzzle;

    /**
     * @var string Unique User identifier
     */
    public $_id;

    /**
     * @var array The User profileIds
     */
    public $profileIds;


    /**
     * User constructor.
     * @param kuzzle $kuzzle An instantiated Kuzzle object
     * @param string $_id Unique profile identifier
     * @param array $profileIds User profileIds
     * @return User
     */
    public function __construct(Kuzzle $kuzzle, $_id = '', array $profileIds = [])
    {
        $this->kuzzle = $kuzzle;
        $this->_id = $_id;
        $this->profileIds = $profileIds;

        return $this;
    }

    /**
     * Get user associated profiles
     *
     * @param array $options Optional Options
     * @return Profiles[]
     */
    public function getProfiles(array $options = [])
    {
        if (empty($this->profileIds) || !$this->profileIds) {
            return [];
        }

        return $this->kuzzle->security->mGetProfiles($this->profileIds, $options);
    }
}
