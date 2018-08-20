<?php

namespace Kuzzle\Security;

use InvalidArgumentException;
use Kuzzle\Kuzzle;

/**
 * Class Profile
 * @package kuzzleio/kuzzle-sdk
 */
class Profile
{
    /**
     * @var Kuzzle The kuzzle security instance associated to this Profile
     */
    public $kuzzle;

    /**
     * @var string Unique Profile identifier
     */
    public $_id;

    /**
     * @var array The Profile policies
     */
    public $policies;


    /**
     * Profile constructor.
     * @param Kuzzle\Kuzzle $kuzzle An instantiated Kuzzle object
     * @param string $_id Unique profile identifier
     * @param array $policies Profile policies
     * @return Profile
     */
    public function __construct(Kuzzle $kuzzle, $_id = '', array $policies = [])
    {
        $this->kuzzle = $kuzzle;
        $this->_id = $_id;
        $this->policies = $policies;

        return $this;
    }

    public function getRoles(array $options = [])
    {
        if (empty($this->policies) || !$this->policies) {
            return [];
        }

        return $this->kuzzle->security->mGetRoles($this->forgeRolesIdsArray(), $options);
    }

    private function forgeRolesIdsArray()
    {
        $ids = [];
        foreach ($this->policies as $key => $policy) {
            array_push($ids, $policy['_id']);
        }

        return $ids;
    }
}
