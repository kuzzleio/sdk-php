<?php

namespace Kuzzle\Util;

use Kuzzle\Security\Profile;

/**
 * Class ProfilesSearchResult
 * @package Kuzzle\Util
 */
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
     * @param string|null $scrollId
     */
    public function __construct($total, array $profiles, $scrollId = null)
    {
        $this->total = $total;
        $this->profiles = $profiles;
        $this->scrollId = $scrollId;

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

    /**
     * @return null|string
     */
    public function getScrollId()
    {
        return $this->scrollId;
    }
}
