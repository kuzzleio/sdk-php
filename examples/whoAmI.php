<?php
include __DIR__ . '/../tests/bootstrap.php';

$kuzzle = new \Kuzzle\Kuzzle('localhost');

$response = $kuzzle->login('local', ['username' => 'test', 'password' => 'testpwd']);
var_dump($response);

$response = $kuzzle->whoAmI()->serialize();
var_dump($response);

$response = $kuzzle->logout();
var_dump($response);

$response = $kuzzle->whoAmI()->serialize();
var_dump($response);
