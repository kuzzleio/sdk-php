<?php
include "../vendor/autoload.php";
include "../src/kuzzle.php";
include "../src/document.php";
include "../src/dataMapping.php";
include "../src/dataCollection.php";
include "../src/memoryStorage.php";
include "../src/util/advancedSearchResult.php";
include "../src/security/security.php";
include "../src/security/role.php";
include "../src/security/profile.php";
include "../src/security/user.php";

$kuzzle = new \Kuzzle\Kuzzle('http://localhost:7511');

$response = $kuzzle->login('local', ['username' => 'test', 'password' => 'testpwd']);
var_dump($response);

$response = $kuzzle->whoAmI();
var_dump($response);

$response = $kuzzle->logout();
var_dump($response);

$response = $kuzzle->whoAmI();
var_dump($response);