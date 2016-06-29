<?php
include __DIR__ . '/../tests/bootstrap.php';

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

// get document
$document = $collection->fetchDocument($document->getId());
var_dump($document->serialize());

// search all documents
$searchResult = $collection->advancedSearch([]);

// count without fetch
$searchCount = $collection->count([]);
var_dump('documents found:', $searchResult->getTotal(), $searchCount);

foreach ($searchResult->getDocuments() as $document) {
    var_dump($document->serialize());
}