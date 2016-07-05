<?php

namespace Kuzzle\Util;

interface RequestInterface
{
    public function execute(array $parameters = []);
}
