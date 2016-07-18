<?php

namespace Kuzzle\Security;

class Policy
{
    /**
     * @var Profile associated profile
     */
    protected $profile;

    /**
     * @var string associated roleId
     */
    protected $roleId;

    /**
     * @var array[]
     */
    protected $restrictedTo = [];

    /**
     * @var boolean
     */
    protected $allowInternalIndex;

    /**
     * Policy constructor.
     *
     * @param Profile $profile
     * @param Role|string $role
     * @param array $restrictedTo
     * @param bool $allowInternalIndex
     * @return Policy
     */
    public function __construct(Profile $profile, $role, array $restrictedTo = [], $allowInternalIndex = false)
    {
        $this->profile = $profile;
        $this->setRole($role);
        $this->setRestrictedTo($restrictedTo);
        $this->setAllowInternalIndex($allowInternalIndex);

        return $this;
    }

    /**
     * @param Role|string $role
     * @return Policy
     */
    public function setRole($role)
    {
        if ($role instanceof Role) {
            $this->roleId = $role->getId();
        } else {
            $this->roleId = $role;
        }

        return $this;
    }

    /**
     * @param array[] $restrictedTo
     * @return Policy
     */
    public function setRestrictedTo(array $restrictedTo)
    {
        $this->restrictedTo = $restrictedTo;
        
        return $this;
    }

    /**
     * @param array $restriction
     * @return Policy
     */
    public function addRestriction(array $restriction)
    {
        $this->restrictedTo[] = $restriction;

        return $this;
    }

    /**
     * @param $allowInternalIndex
     * @return Policy
     */
    public function setAllowInternalIndex($allowInternalIndex)
    {
        $this->allowInternalIndex = $allowInternalIndex;

        return $this;
    }

    /**
     * @return Role
     */
    public function getRole()
    {
        return $this->profile->getSecurity()->getRole($this->roleId);
    }

    /**
     * @return Profile
     */
    public function getProfile()
    {
        return $this->profile;
    }

    /**
     * @return array
     */
    public function serialize()
    {
        $data = [];

        $data['roleId'] = $this->roleId;
        $data['restrictedTo'] = $this->restrictedTo;
        $data['allowInternalIndex'] = $this->allowInternalIndex;

        return $data;
    }
}
