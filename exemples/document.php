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

// enabling auto refresh (avoiding sleep between add/deletion and search)
$kuzzle->setAutoRefresh('myindex');

$collection = $kuzzle->dataCollectionFactory('mycollection', 'myindex');

// delete documents in collection
$deleteResult = $collection->deleteDocument([]);
var_dump($deleteResult);

// add one document
$document = $collection->createDocument(['foo' => 'bar']);
var_dump($document->serialize());

// search all documents
$searchResult = $collection->advancedSearch([]);

// count without fetch
$searchCount = $collection->count([]);
var_dump('documents found:', $searchResult->getTotal(), $searchCount);

foreach ($searchResult->getDocuments() as $document) {
    var_dump($document->serialize());
}