<?php
include "../vendor/autoload.php";
include "../src/Kuzzle.php";
include "../src/Document.php";
include "../src/DataMapping.php";
include "../src/DataCollection.php";
include "../src/MemoryStorage.php";
include "../src/Util/AdvancedSearchResult.php";
include "../src/Util/ProfilesSearchResult.php";
include "../src/Util/RolesSearchResult.php";
include "../src/Util/UsersSearchResult.php";
include "../src/Security/Security.php";
include "../src/Security/Role.php";
include "../src/Security/Profile.php";
include "../src/Security/User.php";

$kuzzle = new \Kuzzle\Kuzzle('http://localhost:7511');

$response = $kuzzle->login('local', ['username' => 'test', 'password' => 'testpwd']);
var_dump($response);

$response = $kuzzle->whoAmI()->serialize();
var_dump($response);

$response = $kuzzle->logout();
var_dump($response);

$response = $kuzzle->whoAmI()->serialize();
var_dump($response);