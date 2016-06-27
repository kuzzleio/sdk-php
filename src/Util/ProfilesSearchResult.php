<?php

namespace Kuzzle\Util;

use Kuzzle\Security\Profile;

class ProfilesSearchResult
{
    /**
     * @var integer
     */
    private $total = 0;

    /**
     * @var Profile[]
     */
    private $profiles = [];

    /**
     * ProfilesSearchResult constructor.
     *
     * @param integer $total
     * @param Profile[] $profiles
     */
    public function __construct($total, array $profiles)
    {
        $this->total = $total;
        $this->profiles = $profiles;

        return $this;
    }

    /**
     * @return integer
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * @return Profile[]
     */
    public function getProfiles()
    {
        return $this->profiles;
    }
}