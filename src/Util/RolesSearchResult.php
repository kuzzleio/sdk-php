<?php

namespace Kuzzle\Util;

use Kuzzle\Security\Role;

class RolesSearchResult
{
    /**
     * @var integer
     */
    private $total = 0;

    /**
     * @var Role[]
     */
    private $roles = [];

    /**
     * RolesSearchResult constructor.
     *
     * @param integer $total
     * @param Role[] $roles
     */
    public function __construct($total, array $roles)
    {
        $this->total = $total;
        $this->roles = $roles;

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
     * @return Role[]
     */
    public function getRoles()
    {
        return $this->roles;
    }
}
