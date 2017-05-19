<?php

namespace Kuzzle\Util;

use Kuzzle\Security\User;

/**
 * Class UsersSearchResult
 * @package Kuzzle\Util
 */
class UsersSearchResult
{
    /**
     * @var integer
     */
    private $total = 0;

    /**
     * @var User[]
     */
    private $users = [];

    /**
     * @var null|string
     */
    private $scrollId = null;

    /**
     * UsersSearchResult constructor.
     *
     * @param integer $total
     * @param User[] $users
     * @param string|null $scrollId
     */
    public function __construct($total, array $users, $scrollId = null)
    {
        $this->total = $total;
        $this->users = $users;
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
     * @return User[]
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * @return null|string
     */
    public function getScrollId()
    {
        return $this->scrollId;
    }
}
