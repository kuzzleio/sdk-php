<?php
use Kuzzle\DataCollection;
use Kuzzle\Kuzzle;

class DataCollectionTest extends \PHPUnit_Framework_TestCase
{
    function testSearch()
    {
        $url = KuzzleTest::FAKE_KUZZLE_HOST;
        $requestId = uniqid();
        $index = 'index';
        $collection = 'collection';
        $filter = [
            'query' => [
                'bool' => [
                    'should' => [
                        'term' => ['foo' =>  'bar']
                    ]
                ]
            ]
        ];

        $httpRequest = [
            'route' => '/' . $index . '/' . $collection . '/_search',
            'method' => 'POST',
            'request' => [
                'metadata' => [],
                'controller' => 'document',
                'action' => 'search',
                'requestId' => $requestId,
                'body' => $filter,
                'collection' => $collection,
                'index' => $index
            ],
            'query_parameters' => []
        ];
        $advancedSearchResponse = [
            'hits' => [
                0 => [
                    '_id' => 'test',
                    '_source' => [
                        'foo' => 'bar'
                    ]
                ],
                1 => [
                    '_id' => 'test1',
                    '_source' => [
                        'foo' => 'bar'
                    ]
                ]
            ],
            'aggregations' => [
                'aggs_name' => []
            ],
            'total' => 2
        ];
        $httpResponse = [
            'error' => null,
            'result' => $advancedSearchResponse
        ];

        $kuzzle = $this
            ->getMockBuilder('\Kuzzle\Kuzzle')
            ->setMethods(['emitRestRequest'])
            ->setConstructorArgs([$url])
            ->getMock();

        $kuzzle
            ->expects($this->once())
            ->method('emitRestRequest')
            ->with($httpRequest)
            ->willReturn($httpResponse);

        /**
         * @var Kuzzle $kuzzle
         */
        $dataCollection = new DataCollection($kuzzle, $collection, $index);

        $searchResult = $dataCollection->search($filter, ['requestId' => $requestId]);

        $this->assertInstanceOf('Kuzzle\Util\SearchResult', $searchResult);
        $this->assertEquals(2, $searchResult->getTotal());

        $documents = $searchResult->getDocuments();
        $this->assertInstanceOf('Kuzzle\Document', $documents[0]);
        $this->assertAttributeEquals('test', 'id', $documents[0]);
        $this->assertAttributeEquals('test1', 'id', $documents[1]);

        $this->assertEquals([
            'aggs_name' => []
        ], $searchResult->getAggregations());
    }

    public function testScroll()
    {
        $url = KuzzleTest::FAKE_KUZZLE_HOST;
        $scrollId = uniqid();
        $index = 'index';
        $collection = 'collection';

        $kuzzle = $this
            ->getMockBuilder('\Kuzzle\Kuzzle')
            ->setMethods(['emitRestRequest'])
            ->setConstructorArgs([$url])
            ->getMock();

        $options = [
            'requestId' => uniqid(),
            'scroll' => '1m'
        ];

        // mock http request
        $httpRequest = [
            'route' => '/_scroll/' . $scrollId,
            'request' => [
                'action' => 'scroll',
                'controller' => 'document',
                'metadata' => [],
                'requestId' => $options['requestId']
            ],
            'method' => 'POST',
            'query_parameters' => [
                'scroll' => '1m'
            ]
        ];

        // mock response
        $scrollResponse = [
            'hits' => [
                0 => [
                    '_id' => 'test',
                    '_source' => [
                        'foo' => 'bar'
                    ]
                ],
                1 => [
                    '_id' => 'test1',
                    '_source' => [
                        'foo' => 'bar'
                    ]
                ]
            ],
            'aggregations' => [
                'aggs_name' => []
            ],
            'total' => 2
        ];
        $httpResponse = [
            'error' => null,
            'result' => $scrollResponse
        ];

        $kuzzle
            ->expects($this->once())
            ->method('emitRestRequest')
            ->with($httpRequest)
            ->willReturn($httpResponse);

        /**
         * @var \Kuzzle\Kuzzle $kuzzle
         */
        $dataCollection = new DataCollection($kuzzle, $collection, $index);

        $searchResult = $dataCollection->scroll($scrollId, $options);

        $this->assertInstanceOf('Kuzzle\Util\SearchResult', $searchResult);
        $this->assertEquals(2, $searchResult->getTotal());

        $documents = $searchResult->getDocuments();
        $this->assertInstanceOf('Kuzzle\Document', $documents[0]);
        $this->assertAttributeEquals('test', 'id', $documents[0]);
        $this->assertAttributeEquals('test1', 'id', $documents[1]);

        $this->assertEquals([
            'aggs_name' => []
        ], $searchResult->getAggregations());
    }

    function testCount()
    {
        $url = KuzzleTest::FAKE_KUZZLE_HOST;
        $requestId = uniqid();
        $index = 'index';
        $collection = 'collection';
        $filter = [
            'query' => [
                'bool' => [
                    'should' => [
                        'term' => ['foo' =>  'bar']
                    ]
                ]
            ]
        ];

        $httpRequest = [
            'route' => '/' . $index . '/' . $collection . '/_count',
            'method' => 'POST',
            'request' => [
                'metadata' => [],
                'controller' => 'document',
                'action' => 'count',
                'requestId' => $requestId,
                'body' => $filter,
                'collection' => $collection,
                'index' => $index
            ],
            'query_parameters' => []
        ];
        $countResponse = [
            'count' => 2
        ];
        $httpResponse = [
            'error' => null,
            'result' => $countResponse
        ];

        $kuzzle = $this
            ->getMockBuilder('\Kuzzle\Kuzzle')
            ->setMethods(['emitRestRequest'])
            ->setConstructorArgs([$url])
            ->getMock();

        $kuzzle
            ->expects($this->once())
            ->method('emitRestRequest')
            ->with($httpRequest)
            ->willReturn($httpResponse);

        /**
         * @var Kuzzle $kuzzle
         */
        $dataCollection = new DataCollection($kuzzle, $collection, $index);

        $count = $dataCollection->count($filter, ['requestId' => $requestId]);

        $this->assertEquals(2, $count);
    }

    function testCreate()
    {
        $url = KuzzleTest::FAKE_KUZZLE_HOST;
        $requestId = uniqid();
        $index = 'index';
        $collection = 'collection';

        $httpRequest = [
            'route' => '/' . $index . '/' . $collection ,
            'method' => 'PUT',
            'request' => [
                'metadata' => [],
                'controller' => 'collection',
                'action' => 'create',
                'requestId' => $requestId,
                'collection' => $collection,
                'index' => $index
            ],
            'query_parameters' => []
        ];
        $createCollectionResponse = [
            'acknowledged' => true
        ];
        $httpResponse = [
            'error' => null,
            'result' => $createCollectionResponse
        ];

        $kuzzle = $this
            ->getMockBuilder('\Kuzzle\Kuzzle')
            ->setMethods(['emitRestRequest'])
            ->setConstructorArgs([$url])
            ->getMock();

        $kuzzle
            ->expects($this->once())
            ->method('emitRestRequest')
            ->with($httpRequest)
            ->willReturn($httpResponse);

        /**
         * @var Kuzzle $kuzzle
         */
        $dataCollection = new DataCollection($kuzzle, $collection, $index);

        $result = $dataCollection->create(['requestId' => $requestId]);

        $this->assertEquals(true, $result);
    }

    function testCreateDocument()
    {
        $url = KuzzleTest::FAKE_KUZZLE_HOST;
        $requestId = uniqid();
        $index = 'index';
        $collection = 'collection';

        $documentId = uniqid();
        $documentContent = [
            'foo' => 'bar'
        ];

        $httpRequest = [
            'route' => '/' . $index . '/' . $collection . '/' . $documentId . '/_create',
            'method' => 'PUT',
            'request' => [
                'metadata' => [],
                'controller' => 'document',
                'action' => 'create',
                'body' => $documentContent,
                'requestId' => $requestId,
                'collection' => $collection,
                'index' => $index,
                '_id' => $documentId
            ],
            'query_parameters' => []
        ];
        $createDocumentResponse = [
            '_id' => $documentId,
            '_source' => $documentContent,
            '_version' => 1
        ];
        $httpResponse = [
            'error' => null,
            'result' => $createDocumentResponse
        ];

        $kuzzle = $this
            ->getMockBuilder('\Kuzzle\Kuzzle')
            ->setMethods(['emitRestRequest'])
            ->setConstructorArgs([$url])
            ->getMock();

        $kuzzle
            ->expects($this->once())
            ->method('emitRestRequest')
            ->with($httpRequest)
            ->willReturn($httpResponse);

        /**
         * @var Kuzzle $kuzzle
         */
        $dataCollection = new DataCollection($kuzzle, $collection, $index);

        $document = $dataCollection->createDocument($documentContent, $documentId, ['requestId' => $requestId]);

        $this->assertInstanceOf('Kuzzle\Document', $document);
        $this->assertAttributeEquals($documentId, 'id', $document);
        $this->assertAttributeEquals($documentContent, 'content', $document);
        $this->assertAttributeEquals(1, 'version', $document);
    }

    function testCreateDocumentFromObject()
    {
        $url = KuzzleTest::FAKE_KUZZLE_HOST;
        $requestId = uniqid();
        $index = 'index';
        $collection = 'collection';

        $documentId = uniqid();
        $documentContent = [
            'foo' => 'bar'
        ];

        $httpRequest = [
            'route' => '/' . $index . '/' . $collection . '/' . $documentId,
            'method' => 'PUT',
            'request' => [
                'metadata' => [],
                'controller' => 'document',
                'action' => 'createOrReplace',
                'body' => $documentContent,
                'requestId' => $requestId,
                'collection' => $collection,
                'index' => $index,
                '_id' => $documentId
            ],
            'query_parameters' => []
        ];
        $createDocumentResponse = [
            '_id' => $documentId,
            '_source' => $documentContent,
            '_version' => 1
        ];
        $httpResponse = [
            'error' => null,
            'result' => $createDocumentResponse
        ];

        $kuzzle = $this
            ->getMockBuilder('\Kuzzle\Kuzzle')
            ->setMethods(['emitRestRequest'])
            ->setConstructorArgs([$url])
            ->getMock();

        $kuzzle
            ->expects($this->once())
            ->method('emitRestRequest')
            ->with($httpRequest)
            ->willReturn($httpResponse);

        /**
         * @var Kuzzle $kuzzle
         */
        $dataCollection = new DataCollection($kuzzle, $collection, $index);

        $documentObject = new \Kuzzle\Document($dataCollection, $documentId, $documentContent);

        $document = $dataCollection->createDocument($documentObject, '', ['updateIfExist' => true, 'requestId' => $requestId]);

        $this->assertInstanceOf('Kuzzle\Document', $document);
        $this->assertAttributeEquals($documentId, 'id', $document);
        $this->assertAttributeEquals($documentContent, 'content', $document);
        $this->assertAttributeEquals(1, 'version', $document);
    }

    function testDeleteDocument()
    {
        $url = KuzzleTest::FAKE_KUZZLE_HOST;
        $requestId = uniqid();
        $index = 'index';
        $collection = 'collection';

        $documentId = uniqid();

        $httpRequest = [
            'route' => '/' . $index . '/' . $collection . '/' . $documentId,
            'method' => 'DELETE',
            'request' => [
                'metadata' => [],
                'controller' => 'document',
                'action' => 'delete',
                'requestId' => $requestId,
                'collection' => $collection,
                'index' => $index,
                '_id' => $documentId
            ],
            'query_parameters' => []
        ];

        $deleteDocumentResponse = [
            '_id' => $documentId,
        ];
        $httpResponse = [
            'error' => null,
            'result' => $deleteDocumentResponse
        ];

        $kuzzle = $this
            ->getMockBuilder('\Kuzzle\Kuzzle')
            ->setMethods(['emitRestRequest'])
            ->setConstructorArgs([$url])
            ->getMock();

        $kuzzle
            ->expects($this->once())
            ->method('emitRestRequest')
            ->with($httpRequest)
            ->willReturn($httpResponse);

        /**
         * @var Kuzzle $kuzzle
         */
        $dataCollection = new DataCollection($kuzzle, $collection, $index);

        $result = $dataCollection->deleteDocument($documentId, ['requestId' => $requestId]);

        $this->assertEquals($result, $documentId);
    }

    function testDeleteDocumentByQuery()
    {
        $url = KuzzleTest::FAKE_KUZZLE_HOST;
        $requestId = uniqid();
        $index = 'index';
        $collection = 'collection';

        $documentId = uniqid();
        $filters = [];

        $httpRequest = [
            'route' => '/' . $index . '/' . $collection . '/_query',
            'method' => 'DELETE',
            'request' => [
                'metadata' => [],
                'controller' => 'document',
                'action' => 'deleteByQuery',
                'requestId' => $requestId,
                'collection' => $collection,
                'index' => $index,
                'body' => (object)$filters
            ],
            'query_parameters' => []
        ];

        $deleteDocumentResponse = [
            'ids' => [$documentId],
        ];
        $httpResponse = [
            'error' => null,
            'result' => $deleteDocumentResponse
        ];

        $kuzzle = $this
            ->getMockBuilder('\Kuzzle\Kuzzle')
            ->setMethods(['emitRestRequest'])
            ->setConstructorArgs([$url])
            ->getMock();

        $kuzzle
            ->expects($this->once())
            ->method('emitRestRequest')
            ->with($httpRequest)
            ->willReturn($httpResponse);

        /**
         * @var Kuzzle $kuzzle
         */
        $dataCollection = new DataCollection($kuzzle, $collection, $index);

        $result = $dataCollection->deleteDocument($filters, ['requestId' => $requestId]);

        $this->assertEquals($result, [$documentId]);
    }

    function testFetchDocument()
    {
        $url = KuzzleTest::FAKE_KUZZLE_HOST;
        $requestId = uniqid();
        $index = 'index';
        $collection = 'collection';

        $documentId = uniqid();
        $documentContent = [
            'foo' => 'bar'
        ];

        $httpRequest = [
            'route' => '/' . $index . '/' . $collection . '/' . $documentId,
            'method' => 'GET',
            'request' => [
                'metadata' => [],
                'controller' => 'document',
                'action' => 'get',
                'requestId' => $requestId,
                'collection' => $collection,
                'index' => $index,
                '_id' => $documentId
            ],
            'query_parameters' => []
        ];
        $fetchDocumentResponse = [
            '_id' => $documentId,
            '_source' => $documentContent,
            '_version' => 1
        ];
        $httpResponse = [
            'error' => null,
            'result' => $fetchDocumentResponse
        ];

        $kuzzle = $this
            ->getMockBuilder('\Kuzzle\Kuzzle')
            ->setMethods(['emitRestRequest'])
            ->setConstructorArgs([$url])
            ->getMock();

        $kuzzle
            ->expects($this->once())
            ->method('emitRestRequest')
            ->with($httpRequest)
            ->willReturn($httpResponse);

        /**
         * @var Kuzzle $kuzzle
         */
        $dataCollection = new DataCollection($kuzzle, $collection, $index);

        $document = $dataCollection->fetchDocument($documentId, ['requestId' => $requestId]);

        $this->assertInstanceOf('Kuzzle\Document', $document);
        $this->assertAttributeEquals($documentId, 'id', $document);
        $this->assertAttributeEquals($documentContent, 'content', $document);
        $this->assertAttributeEquals(1, 'version', $document);
    }

    function testFetchAllDocumentsWithScroll()
    {
        $url = KuzzleTest::FAKE_KUZZLE_HOST;
        $requestId = uniqid();
        $scrollId = uniqid();
        $index = 'index';
        $collection = 'collection';

        $httpSearchRequest = [
            'route' => '/' . $index . '/' . $collection . '/_search',
            'method' => 'POST',
            'request' => [
                'metadata' => [],
                'controller' => 'document',
                'action' => 'search',
                'requestId' => $requestId,
                'body' => (object)[],
                'collection' => $collection,
                'index' => $index
            ],
            'query_parameters' => [
                'from' => 0,
                'size' => 1,
                'scroll' => '30s'
            ]
        ];

        $httpScrollRequest = [
            'route' => '/_scroll/' . $scrollId,
            'method' => 'POST',
            'request' => [
                'metadata' => [],
                'controller' => 'document',
                'action' => 'scroll',
                'requestId' => $requestId,
            ],
            'query_parameters' => [
                'scroll' => '30s',
                'from' => 0,
                'size' => 1,
            ]
        ];
        $searchResponse = [
            'hits' => [
                0 => [
                    '_id' => 'test',
                    '_source' => [
                        'foo' => 'bar'
                    ]
                ]
            ],
            '_scroll_id' => $scrollId,
            'total' => 2
        ];
        $scrollResponse = [
            'hits' => [
                0 => [
                    '_id' => 'test1',
                    '_source' => [
                        'foo' => 'bar'
                    ]
                ]
            ],
            'total' => 2
        ];
        $httpSearchResponse = [
            'error' => null,
            'result' => $searchResponse
        ];
        $httpScrollResponse = [
            'error' => null,
            'result' => $scrollResponse
        ];

        $kuzzle = $this
            ->getMockBuilder('\Kuzzle\Kuzzle')
            ->setMethods(['emitRestRequest'])
            ->setConstructorArgs([$url])
            ->getMock();

        $kuzzle
            ->expects($this->at(0))
            ->method('emitRestRequest')
            ->with($httpSearchRequest)
            ->willReturn($httpSearchResponse);

        $kuzzle
            ->expects($this->at(1))
            ->method('emitRestRequest')
            ->with($httpScrollRequest)
            ->willReturn($httpScrollResponse);

        /**
         * @var Kuzzle $kuzzle
         */
        $dataCollection = new DataCollection($kuzzle, $collection, $index);

        $documents = $dataCollection->fetchAllDocuments(['from' => 0, 'size' => 1, 'scroll' => '30s', 'requestId' => $requestId]);

        $this->assertInternalType('array', $documents);
        $this->assertEquals(2, count($documents));

        $this->assertInstanceOf('Kuzzle\Document', $documents[0]);
        $this->assertAttributeEquals('test', 'id', $documents[0]);
        $this->assertAttributeEquals('test1', 'id', $documents[1]);
    }

    function testFetchAllDocumentsWithoutScroll()
    {
        $url = KuzzleTest::FAKE_KUZZLE_HOST;
        $requestId = uniqid();
        $index = 'index';
        $collection = 'collection';

        $httpSearchRequest = [
            'route' => '/' . $index . '/' . $collection . '/_search',
            'method' => 'POST',
            'request' => [
                'metadata' => [],
                'controller' => 'document',
                'action' => 'search',
                'requestId' => $requestId,
                'body' => (object)[],
                'collection' => $collection,
                'index' => $index
            ],
            'query_parameters' => [
                'from' => 0,
                'size' => 1
            ]
        ];

        $httpSecondSearchRequest = [
            'route' => '/' . $index . '/' . $collection . '/_search',
            'method' => 'POST',
            'request' => [
                'metadata' => [],
                'controller' => 'document',
                'action' => 'search',
                'requestId' => $requestId,
                'body' => (object)[],
                'collection' => $collection,
                'index' => $index
            ],
            'query_parameters' => [
                'from' => 1,
                'size' => 1
            ]
        ];
        $searchResponse = [
            'hits' => [
                0 => [
                    '_id' => 'test',
                    '_source' => [
                        'foo' => 'bar'
                    ]
                ]
            ],
            'total' => 2
        ];
        $httpSecondSearchResponse = [
            'hits' => [
                0 => [
                    '_id' => 'test1',
                    '_source' => [
                        'foo' => 'bar'
                    ]
                ]
            ],
            'total' => 2
        ];
        $httpSearchResponse = [
            'error' => null,
            'result' => $searchResponse
        ];
        $httpSecondSearchResponse = [
            'error' => null,
            'result' => $httpSecondSearchResponse
        ];

        $kuzzle = $this
            ->getMockBuilder('\Kuzzle\Kuzzle')
            ->setMethods(['emitRestRequest'])
            ->setConstructorArgs([$url])
            ->getMock();

        $kuzzle
            ->expects($this->at(0))
            ->method('emitRestRequest')
            ->with($httpSearchRequest)
            ->willReturn($httpSearchResponse);

        $kuzzle
            ->expects($this->at(1))
            ->method('emitRestRequest')
            ->with($httpSecondSearchRequest)
            ->willReturn($httpSecondSearchResponse);

        /**
         * @var Kuzzle $kuzzle
         */
        $dataCollection = new DataCollection($kuzzle, $collection, $index);

        $documents = $dataCollection->fetchAllDocuments(['from' => 0, 'size' => 1, 'requestId' => $requestId]);

        $this->assertInternalType('array', $documents);
        $this->assertEquals(2, count($documents));

        $this->assertInstanceOf('Kuzzle\Document', $documents[0]);
        $this->assertAttributeEquals('test', 'id', $documents[0]);
        $this->assertAttributeEquals('test1', 'id', $documents[1]);
    }

    function testPublishDocument()
    {
        $url = KuzzleTest::FAKE_KUZZLE_HOST;
        $requestId = uniqid();
        $index = 'index';
        $collection = 'collection';

        $document = [
            'foo' => 'bar'
        ];

        $httpRequest = [
            'route' => '/' . $index . '/' . $collection,
            'method' => 'POST',
            'request' => [
                'metadata' => [],
                'controller' => 'realtime',
                'action' => 'publish',
                'body' => $document,
                'requestId' => $requestId,
                'collection' => $collection,
                'index' => $index
            ],
            'query_parameters' => []
        ];
        $publishDocumentResponse = [
            'published' => true
        ];
        $httpResponse = [
            'error' => null,
            'result' => $publishDocumentResponse
        ];

        $kuzzle = $this
            ->getMockBuilder('\Kuzzle\Kuzzle')
            ->setMethods(['emitRestRequest'])
            ->setConstructorArgs([$url])
            ->getMock();

        $kuzzle
            ->expects($this->once())
            ->method('emitRestRequest')
            ->with($httpRequest)
            ->willReturn($httpResponse);

        /**
         * @var Kuzzle $kuzzle
         */
        $dataCollection = new DataCollection($kuzzle, $collection, $index);

        $result = $dataCollection->publishMessage($document, ['requestId' => $requestId]);

        $this->assertEquals(true, $result);
    }

    function testPublishMessageFromObject()
    {
        $url = KuzzleTest::FAKE_KUZZLE_HOST;
        $requestId = uniqid();
        $index = 'index';
        $collection = 'collection';

        $documentId = uniqid();
        $document = [
            'foo' => 'bar'
        ];

        $httpRequest = [
            'route' => '/' . $index . '/' . $collection,
            'method' => 'POST',
            'request' => [
                'metadata' => [],
                'controller' => 'realtime',
                'action' => 'publish',
                'body' => $document,
                'requestId' => $requestId,
                'collection' => $collection,
                'index' => $index,
                '_id' => $documentId
            ],
            'query_parameters' => []
        ];
        $publishDocumentResponse = [
            'published' => true
        ];
        $httpResponse = [
            'error' => null,
            'result' => $publishDocumentResponse
        ];

        $kuzzle = $this
            ->getMockBuilder('\Kuzzle\Kuzzle')
            ->setMethods(['emitRestRequest'])
            ->setConstructorArgs([$url])
            ->getMock();

        $kuzzle
            ->expects($this->once())
            ->method('emitRestRequest')
            ->with($httpRequest)
            ->willReturn($httpResponse);

        /**
         * @var Kuzzle $kuzzle
         */
        $dataCollection = new DataCollection($kuzzle, $collection, $index);

        $documentObject = new \Kuzzle\Document($dataCollection, $documentId, $document);

        $result = $dataCollection->publishMessage($documentObject, ['requestId' => $requestId]);

        $this->assertEquals(true, $result);
    }

    function testReplaceDocument()
    {
        $url = KuzzleTest::FAKE_KUZZLE_HOST;
        $requestId = uniqid();
        $index = 'index';
        $collection = 'collection';

        $documentId = uniqid();
        $documentContent = [
            'foo' => 'bar'
        ];

        $httpRequest = [
            'route' => '/' . $index . '/' . $collection . '/' . $documentId,
            'method' => 'PUT',
            'request' => [
                'metadata' => [],
                'controller' => 'document',
                'action' => 'createOrReplace',
                'body' => $documentContent,
                'requestId' => $requestId,
                'collection' => $collection,
                'index' => $index,
                '_id' => $documentId
            ],
            'query_parameters' => []
        ];
        $createDocumentResponse = [
            '_id' => $documentId,
            '_source' => $documentContent,
            '_version' => 1
        ];
        $httpResponse = [
            'error' => null,
            'result' => $createDocumentResponse
        ];

        $kuzzle = $this
            ->getMockBuilder('\Kuzzle\Kuzzle')
            ->setMethods(['emitRestRequest'])
            ->setConstructorArgs([$url])
            ->getMock();

        $kuzzle
            ->expects($this->once())
            ->method('emitRestRequest')
            ->with($httpRequest)
            ->willReturn($httpResponse);

        /**
         * @var Kuzzle $kuzzle
         */
        $dataCollection = new DataCollection($kuzzle, $collection, $index);

        $document = $dataCollection->replaceDocument($documentId, $documentContent, ['requestId' => $requestId]);

        $this->assertInstanceOf('Kuzzle\Document', $document);
        $this->assertAttributeEquals($documentId, 'id', $document);
        $this->assertAttributeEquals($documentContent, 'content', $document);
        $this->assertAttributeEquals(1, 'version', $document);
    }

    function testTruncateCollection()
    {
        $url = KuzzleTest::FAKE_KUZZLE_HOST;
        $requestId = uniqid();
        $index = 'index';
        $collection = 'collection';

        $documentId = uniqid();

        $httpRequest = [
            'route' => '/' . $index . '/' . $collection . '/_truncate',
            'method' => 'DELETE',
            'request' => [
                'metadata' => [],
                'controller' => 'collection',
                'action' => 'truncate',
                'requestId' => $requestId,
                'collection' => $collection,
                'index' => $index,
            ],
            'query_parameters' => []
        ];
        $truncateCollectionResponse = [
            'ids' => [$documentId]
        ];
        $httpResponse = [
            'error' => null,
            'result' => $truncateCollectionResponse
        ];

        $kuzzle = $this
            ->getMockBuilder('\Kuzzle\Kuzzle')
            ->setMethods(['emitRestRequest'])
            ->setConstructorArgs([$url])
            ->getMock();

        $kuzzle
            ->expects($this->once())
            ->method('emitRestRequest')
            ->with($httpRequest)
            ->willReturn($httpResponse);

        /**
         * @var Kuzzle $kuzzle
         */
        $dataCollection = new DataCollection($kuzzle, $collection, $index);

        $result = $dataCollection->truncate(['requestId' => $requestId]);

        $this->assertEquals([$documentId], $result);
    }

    function testUpdateDocument()
    {
        $url = KuzzleTest::FAKE_KUZZLE_HOST;
        $requestId = uniqid();
        $index = 'index';
        $collection = 'collection';

        $documentId = uniqid();
        $documentContent = [
            'foo' => 'bar'
        ];

        $kuzzle = $this
            ->getMockBuilder('\Kuzzle\Kuzzle')
            ->setMethods(['emitRestRequest'])
            ->setConstructorArgs([$url])
            ->getMock();


        $httpUpdateRequest = [
            'route' => '/' . $index . '/' . $collection . '/' . $documentId . '/_update',
            'method' => 'PUT',
            'request' => [
                'metadata' => [],
                'controller' => 'document',
                'action' => 'update',
                'body' => $documentContent,
                'requestId' => $requestId,
                'collection' => $collection,
                'index' => $index,
                '_id' => $documentId,
            ],
            'query_parameters' => []
        ];
        $updateDocumentResponse = [
            '_id' => $documentId,
            '_version' => 2
        ];
        $httpUpdateResponse = [
            'error' => null,
            'result' => $updateDocumentResponse
        ];

        $httpGetRequest = [
            'route' => '/' . $index . '/' . $collection . '/' . $documentId,
            'method' => 'GET',
            'request' => [
                'metadata' => [],
                'controller' => 'document',
                'action' => 'get',
                'requestId' => $requestId,
                'collection' => $collection,
                'index' => $index,
                '_id' => $documentId,
            ],
            'query_parameters' => []
        ];
        $getResponse = [
            '_id' => $documentId,
            '_source' => $documentContent,
            '_version' => 2
        ];
        $httpGetResponse = [
            'error' => null,
            'result' => $getResponse
        ];

        $kuzzle
            ->expects($this->at(0))
            ->method('emitRestRequest')
            ->with($httpUpdateRequest)
            ->willReturn($httpUpdateResponse);

        $kuzzle
            ->expects($this->at(1))
            ->method('emitRestRequest')
            ->with($httpGetRequest)
            ->willReturn($httpGetResponse);

        /**
         * @var Kuzzle $kuzzle
         */
        $dataCollection = new DataCollection($kuzzle, $collection, $index);

        $document = $dataCollection->updateDocument($documentId, $documentContent, ['requestId' => $requestId]);

        $this->assertInstanceOf('Kuzzle\Document', $document);
        $this->assertAttributeEquals($documentId, 'id', $document);
        $this->assertAttributeEquals($documentContent, 'content', $document);
        $this->assertAttributeEquals(2, 'version', $document);
    }
}
