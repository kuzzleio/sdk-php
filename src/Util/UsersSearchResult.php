<?php

namespace Kuzzle\Util;

use Kuzzle\Security\User;

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
     * UsersSearchResult constructor.
     *
     * @param integer $total
     * @param User[] $users
     */
    public function __construct($total, array $users)
    {
        $this->total = $total;
        $this->users = $users;

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
}