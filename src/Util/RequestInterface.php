<?php

namespace Kuzzle\Util;

/**
 * Interface RequestInterface
 * @package Kuzzle\Util
 */
interface RequestInterface
{
    /**
     * @param array $parameters
     * @return array
     */
    public function execute(array $parameters = []);
}
