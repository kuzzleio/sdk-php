<?php
include __DIR__ . '/../tests/bootstrap.php';

$kuzzle = new \Kuzzle\Kuzzle('localhost');

$collection = 'mycollection';
$index = 'myindex';

// enabling auto refresh (avoiding sleep between add/deletion and search)
$kuzzle->setAutoRefresh($index);

$collection = $kuzzle->collection($collection, $index);

// delete documents in collection
try {
    $deleteResult = $collection->deleteDocument([]);
    print_r('delete documents in collection');
    print_r("\n");
    print_r($deleteResult);
} catch (ErrorException $e) {
    $kuzzle->createIndex($index);
    $collection->create();
}

// add a document
print_r('add a document');
print_r("\n");
$document = new \Kuzzle\Document($collection);
$document->setContent(['foo'=>'bar']);
$document->create();
print_r($document->serialize());

// add another document
print_r('add another document');
print_r("\n");
$document = new \Kuzzle\Document($collection);
$document->setContent(['foo'=>'baz']);
$document->create();
print_r($document->serialize());

// add a third document
print_r('add a third document');
print_r("\n");
$document = new \Kuzzle\Document($collection);
$document->setContent(['foo'=>'qux', 'oof'=>'foo']);
$document->create();
print_r($document->serialize());

// update third document
print_r('update third document');
print_r("\n");
$document->setContent(['foo'=>'updated']);
$document->update();
print_r($document->serialize());

// replace third document
print_r('replace third document');
print_r("\n");
$document->setContent(['foo'=>'replaced'], true);
$document->replace();
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
    'query' => [
        'bool' => [
            'should' => [
                'exists' => ['field' => 'foo']
            ]
        ]
    ]
];
$options = [
    'scroll' => '30s',
    'from' => 0,
    'size' => 1
];

$searchResult = $collection->search($filter, $options);
$nbDocs = 0;

while ($searchResult) {
    foreach ($searchResult->getDocuments() as $document) {
        print_r($document->serialize());
        $nbDocs++;
    }
    $searchResult = $searchResult->fetchNext();
}
print_r('search with scroll results total:' . $nbDocs . "\n");

// search without scroll results
print_r('search without scroll results:');
print_r("\n");

$filter = [
    'query' => [
        'bool' => [
            'should' => [
                'exists' => ['field' => 'foo']
            ]
        ]
    ]
];
$options = [
    'from' => 0,
    'size' => 1
];

$searchResult = $collection->search($filter, $options);
$nbDocs = 0;

while ($searchResult) {
    foreach ($searchResult->getDocuments() as $document) {
        print_r($document->serialize());
        $nbDocs++;
    }
    $searchResult = $searchResult->fetchNext();
}
print_r('search without scroll results total:' . $nbDocs . "\n");
