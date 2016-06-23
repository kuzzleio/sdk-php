<?php
include "../vendor/autoload.php";
include "../src/kuzzle.php";
include "../src/document.php";
include "../src/dataMapping.php";
include "../src/dataCollection.php";
include "../src/memoryStorage.php";
include "../src/security/security.php";
include "../src/security/role.php";
include "../src/security/profile.php";
include "../src/security/user.php";

$kuzzle = new \Kuzzle\Kuzzle('http://localhost:7511');
$collection = $kuzzle->dataCollectionFactory('mycollection', 'myindex');
$deleteResult = $collection->deleteDocument([]);
sleep(1);
$document = $collection->createDocument(['foo' => 'bar']);
sleep(1);
$searchResult = $collection->advancedSearch([]);
$searchCount = $collection->count([]);

var_dump($document->serialize(), $searchResult, $deleteResult, $searchCount);
