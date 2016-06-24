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

foreach ($searchResult->getDocuments() as $document)
{
    var_dump($document->serialize());
}