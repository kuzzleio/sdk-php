<?php

namespace Kuzzle\Security;

use InvalidArgumentException;

/**
 * Class Profile
 * @package kuzzleio/kuzzle-sdk
 */
class Profile extends Document
{
    protected $deleteActionName = 'deleteProfile';

    protected $updateActionName = 'updateProfile';

    protected $saveActionName = 'createOrReplaceProfile';

    /**
     * @var Policy[]
     */
    protected $policies;

    /**
     * Role constructor.
     * @param Security $kuzzleSecurity An instantiated Kuzzle\Security object
     * @param string $id Unique profile identifier
     * @param array $content Profile content
     * @return Profile
     */
    public function __construct(Security $kuzzleSecurity, $id = '', array $content = [])
    {
        parent::__construct($kuzzleSecurity, $id, $content);

        $this->syncPolicies();

        return $this;
    }

    /**
     * Adds a policy to the profile.
     *
     * @param array|Policy $policy Unique Kuzzle\Security\Policy instance corresponding to the new associated policy
     * @return Profile
     */
    public function addPolicy($policy)
    {
        $this->policies[] = $this->policyFactory($policy);

        return $this;
    }

    /**
     * Returns this profile associated roles.
     *
     * @return Policy[]
     */
    public function getPolicies()
    {
        return $this->policies;
    }

    /**
     * Replaces the roles associated to the profile.
     *
     * @param array[]|Role[] $policies List of policies descriptions or Kuzzle\Security\Role instances corresponding to the new associated roles
     * @return Profile
     */
    public function setPolicies(array $policies)
    {
        $this->policies = [];
        
        foreach ($policies as $policy) {
            $this->policies[] = $this->policyFactory($policy);
        }
        
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

        $this->syncPolicies();

        return $this;
    }

    /**
     * @return array
     */
    public function serialize()
    {
        $data = [];

        if (!empty($this->id)) {
            $data['_id'] = $this->id;
        }

        $this->content['policies'] = [];
        foreach ($this->policies as $policy) {
            $this->content['policies'][] = $policy->serialize();
        }

        $data['body'] = $this->content;


        return $data;
    }

    protected function syncPolicies()
    {
        if (!array_key_exists('policies', $this->content)) {
            $this->content['policies'] = [];
        }

        $this->policies = [];
        foreach ($this->content['policies'] as $policy) {
            $this->policies[] = $this->policyFactory($policy);
        }
    }

    /**
     * @param Policy|array $policy
     * @return Policy
     */
    protected function policyFactory($policy)
    {
        if ($policy instanceof Policy) {
            return $policy;
        }

        if (is_array($policy)) {
            $policyObject = new Policy($this, $policy['roleId']);
    
            if (array_key_exists('restrictedTo', $policy)) {
                $policyObject->setRestrictedTo($policy['restrictedTo']);
            }
    
            if (array_key_exists('allowInternalIndex', $policy)) {
                $policyObject->setAllowInternalIndex($policy['allowInternalIndex']);
            }
            
            return $policyObject;
        }
        
        throw new InvalidArgumentException('Unable to extract policy from description: an instance of \Kuzzle\Security\Policy or a array is required');
    }
}
