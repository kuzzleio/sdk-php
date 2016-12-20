<?php
include __DIR__ . '/../tests/bootstrap.php';

$kuzzle = new \Kuzzle\Kuzzle('localhost');

// enabling auto refresh (avoiding sleep between add/deletion and search)
$kuzzle->setAutoRefresh('myindex');

$collection = $kuzzle->dataCollectionFactory('mycollection', 'myindex');


// delete documents in collection
try {
    $deleteResult = $collection->deleteDocument([]);
    print_r('delete documents in collection');
    print_r("\n");
    print_r($deleteResult);
}
catch (ErrorException $e) {
    $kuzzle->createIndex('myindex');
    $collection->create();
}


// add a document
print_r('add a document');
print_r("\n");
$document = $collection->createDocument(['foo' => 'bar']);
print_r($document->serialize());

// add another document
print_r('add another document');
print_r("\n");
$document = $collection->createDocument(['foo' => 'baz']);
print_r($document->serialize());

// add a third document
print_r('add a third document');
print_r("\n");
$document = $collection->createDocument(['foo' => 'qux']);
print_r($document->serialize());

// get document
print_r('get document');
print_r("\n");
$document = $collection->fetchDocument($document->getId());
print_r($document->serialize());

// wait for indexing
sleep(2);

// search with scroll results
print_r('search with scroll results:');
print_r("\n");

$filter = [
    'scroll' => '1m',
    'from' => 0,
    'size' => 1,
    'query' => [
        'bool' => [
            'should' => [
                'exists' => ['field' => 'foo']
            ]
        ]
    ]
];
$searchResult = $collection->search($filter);
$nbDocs = 0;

while ($searchResult) {
    foreach ($searchResult->getDocuments() as $document) {
        print_r($document->serialize());
        $nbDocs++;
    }
    $searchResult = $searchResult->getNext();
}
print_r('search with scroll results total:' . $nbDocs . "\n");

// search without scroll results
print_r('search without scroll results:');
print_r("\n");

$filter = [
    'from' => 0,
    'size' => 1,
    'query' => [
        'bool' => [
            'should' => [
                'exists' => ['field' => 'foo']
            ]
        ]
    ]
];
$searchResult = $collection->search($filter);
$nbDocs = 0;

while ($searchResult) {
    foreach ($searchResult->getDocuments() as $document) {
        print_r($document->serialize());
        $nbDocs++;
    }
    $searchResult = $searchResult->getNext();
}
print_r('search without scroll results total:' . $nbDocs . "\n");
