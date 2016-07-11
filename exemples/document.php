<?php
include __DIR__ . '/../tests/bootstrap.php';

$kuzzle = new \Kuzzle\Kuzzle('http://localhost:7511');

// enabling auto refresh (avoiding sleep between add/deletion and search)
$kuzzle->setAutoRefresh('myindex');

$collection = $kuzzle->dataCollectionFactory('mycollection', 'myindex');


// delete documents in collection
try {
    $deleteResult = $collection->deleteDocument([]);
    var_dump($deleteResult);
}
catch (ErrorException $e) {
    $collection->create();
}


// add a document
$document = $collection->createDocument(['foo' => 'bar']);
var_dump($document->serialize());

// add another document
$document = $collection->createDocument(['foo' => 'baz']);
var_dump($document->serialize());

// get document
$document = $collection->fetchDocument($document->getId());
var_dump($document->serialize());

$filter = [
    'query' => [
        'bool' => [
            'should' => [
                'term' => ['foo' => 'bar']
            ]
        ]
    ]
];

sleep(1);

// search all documents
$searchResult = $collection->advancedSearch($filter);

// count without fetch
$searchCount = $collection->count($filter);
var_dump('documents found:', $searchResult->getTotal(), $searchCount);

foreach ($searchResult->getDocuments() as $document) {
    var_dump($document->serialize());
}