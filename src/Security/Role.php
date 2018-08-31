<?php

namespace Kuzzle\Security;

use Kuzzle\Kuzzle;

/**
 * Class Role
 * @package kuzzleio/kuzzle-sdk
 */
class Role
{
    /**
     * @var Kuzzle The kuzzle security instance associated to this Role
     */
    private $kuzzle;

    /**
     * @var string Unique Role identifier
     */
    public $_id;

    /**
     * @var array The Role controllers
     */
    public $controllers;


    /**
     * Role constructor.
     * @param kuzzle $kuzzle An instantiated Kuzzle object
     * @param string $_id Unique role identifier
     * @param array $controllers Role controllers
     * @return Role
     */
    public function __construct(Kuzzle $kuzzle, $_id = '', array $controllers = [])
    {
        $this->kuzzle = $kuzzle;
        $this->_id = $_id;
        $this->controllers = $controllers;

        return $this;
    }
}
