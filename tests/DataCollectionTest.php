<?php
use Kuzzle\Collection;
use Kuzzle\Kuzzle;

class DataCollectionTest extends \PHPUnit_Framework_TestCase
{
    function testSearch()
    {
        $url = KuzzleTest::FAKE_KUZZLE_HOST;
        $requestId = uniqid();
        $index = 'index';
        $options = ['requestId' => $requestId];
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
                'volatile' => [],
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
                    ],
                    '_meta' => [
                        'author' => 'foo'
                    ]
                ],
                1 => [
                    '_id' => 'test1',
                    '_source' => [
                        'foo' => 'bar'
                    ],
                    '_meta' => [
                        'author' => 'bar'
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
        $dataCollection = new Collection($kuzzle, $collection, $index);

        $searchResult = $dataCollection->search($filter, $options);

        $this->assertInstanceOf('Kuzzle\Util\SearchResult', $searchResult);
        $this->assertEquals(2, $searchResult->getTotal());
        $this->assertEquals($options, $searchResult->getOptions());
        $this->assertEquals($filter, $searchResult->getFilters());
        $this->assertEquals(2, $searchResult->getFetchedDocuments());

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
                'volatile' => [],
                'requestId' => $options['requestId']
            ],
            'method' => 'GET',
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
                    ],
                    '_meta' => [
                        'author' => 'foo'
                    ]
                ],
                1 => [
                    '_id' => 'test1',
                    '_source' => [
                        'foo' => 'bar'
                    ],
                    '_meta' => [
                        'author' => 'bar'
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
        $dataCollection = new Collection($kuzzle, $collection, $index);

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
                'volatile' => [],
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
        $dataCollection = new Collection($kuzzle, $collection, $index);

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
                'volatile' => [],
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
        $dataCollection = new Collection($kuzzle, $collection, $index);

        $result = $dataCollection->create(['requestId' => $requestId]);

        $this->assertEquals(true, $result);
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
                'volatile' => [],
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
        $dataCollection = new Collection($kuzzle, $collection, $index);

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
                'volatile' => [],
                'controller' => 'document',
                'action' => 'deleteByQuery',
                'requestId' => $requestId,
                'collection' => $collection,
                'index' => $index,
                'body' => ['query' => (object)$filters]
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
        $dataCollection = new Collection($kuzzle, $collection, $index);

        $result = $dataCollection->deleteDocument($filters, ['requestId' => $requestId]);

        $this->assertEquals($result, [$documentId]);
    }

    function testDeleteSpecifications()
    {
        $url = KuzzleTest::FAKE_KUZZLE_HOST;
        $requestId = uniqid();
        $index = 'index';
        $collection = 'collection';

        $httpRequest = [
            'route' => '/' . $index . '/' . $collection . '/_specifications',
            'method' => 'DELETE',
            'request' => [
                'volatile' => [],
                'controller' => 'collection',
                'action' => 'deleteSpecifications',
                'requestId' => $requestId,
                'collection' => $collection,
                'index' => $index
            ],
            'query_parameters' => []
        ];

        $httpResponse = [
            'error' => null,
            'result' => [
                'acknowledged' => true
            ]
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
        $dataCollection = new Collection($kuzzle, $collection, $index);

        $result = $dataCollection->deleteSpecifications(['requestId' => $requestId]);

        $this->assertEquals($result, ['acknowledged' => true]);
    }

    function testDocumentExists()
    {
        $url = KuzzleTest::FAKE_KUZZLE_HOST;
        $requestId = uniqid();
        $index = 'index';
        $collection = 'collection';

        $documentId = uniqid();

        $httpRequest = [
            'route' => '/' . $index . '/' . $collection . '/' . $documentId . '/_exists',
            'method' => 'GET',
            'request' => [
                'volatile' => [],
                'controller' => 'document',
                'action' => 'exists',
                'requestId' => $requestId,
                'collection' => $collection,
                'index' => $index,
                '_id' => $documentId
            ],
            'query_parameters' => []
        ];

        $httpResponse = [
            'error' => null,
            'result' => true
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
        $dataCollection = new Collection($kuzzle, $collection, $index);

        $result = $dataCollection->documentExists($documentId, ['requestId' => $requestId]);

        $this->assertEquals(true, $result);
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
        $documentMeta = [
            'author' => 'foo'
        ];

        $httpRequest = [
            'route' => '/' . $index . '/' . $collection . '/' . $documentId,
            'method' => 'GET',
            'request' => [
                'volatile' => [],
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
            '_meta' => $documentMeta,
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
        $dataCollection = new Collection($kuzzle, $collection, $index);

        $document = $dataCollection->fetchDocument($documentId, ['requestId' => $requestId]);

        $this->assertInstanceOf('Kuzzle\Document', $document);
        $this->assertAttributeEquals($documentId, 'id', $document);
        $this->assertAttributeEquals($documentContent, 'content', $document);
        $this->assertAttributeEquals(1, 'version', $document);
    }

    function testGetSpecifications()
    {
        $url = KuzzleTest::FAKE_KUZZLE_HOST;
        $requestId = uniqid();
        $index = 'index';
        $collection = 'collection';

        $specificationsContent = [
            'validation' => [
                'strict' => true,
                'fields' => [
                    'foo' => [
                        'mandatory' => true,
                        'type' => 'string',
                        'defaultValue' => 'bar'
                    ]
                ]
            ]
        ];

        $httpRequest = [
            'route' => '/' . $index . '/' . $collection . '/_specifications',
            'method' => 'GET',
            'request' => [
                'volatile' => [],
                'controller' => 'collection',
                'action' => 'getSpecifications',
                'requestId' => $requestId,
                'collection' => $collection,
                'index' => $index,
            ],
            'query_parameters' => []
        ];
        $getSpecificationsResponse = [
            '_source' => $specificationsContent,
        ];
        $httpResponse = [
            'error' => null,
            'result' => $getSpecificationsResponse
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
        $dataCollection = new Collection($kuzzle, $collection, $index);

        $response = $dataCollection->getSpecifications(['requestId' => $requestId]);

        $this->assertEquals($specificationsContent, $response['_source']);
    }

    function testMCreateDocument()
    {
        $url = KuzzleTest::FAKE_KUZZLE_HOST;
        $requestId = uniqid();
        $index = 'index';
        $collection = 'collection';

        $documents = [
            'documents' => [
                ['_id' => 'foo1', 'foo' => 'bar'],
                ['_id' => 'foo2', 'foo' => 'bar'],
            ]
        ];

        $httpRequest = [
            'route' => '/' . $index . '/' . $collection . '/_mCreate',
            'method' => 'POST',
            'request' => [
                'volatile' => [],
                'controller' => 'document',
                'action' => 'mCreate',
                'body' => $documents,
                'requestId' => $requestId,
                'collection' => $collection,
                'index' => $index
            ],
            'query_parameters' => []
        ];
        $mCreateResponse = [
            '_source' => [
                'hits' => $documents['documents'],
                'total' => count($documents['documents'])
            ]
        ];
        $httpResponse = [
            'error' => null,
            'result' => $mCreateResponse
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
        $dataCollection = new Collection($kuzzle, $collection, $index);

        $result = $dataCollection->mCreateDocument($documents['documents'], ['requestId' => $requestId]);

        $this->assertEquals($httpResponse['result'], $result);
    }

    function testMDeleteDocument()
    {
        $url = KuzzleTest::FAKE_KUZZLE_HOST;
        $requestId = uniqid();
        $index = 'index';
        $collection = 'collection';

        $documentIds = [
            'ids' => ['foo1', 'foo2']
        ];

        $httpRequest = [
            'route' => '/' . $index . '/' . $collection . '/_mDelete',
            'method' => 'DELETE',
            'request' => [
                'volatile' => [],
                'controller' => 'document',
                'action' => 'mDelete',
                'body' => $documentIds,
                'requestId' => $requestId,
                'collection' => $collection,
                'index' => $index
            ],
            'query_parameters' => []
        ];
        $mDeleteResponse = [
            '_source' => [
                'hits' => $documentIds['ids'],
                'total' => count($documentIds['ids'])
            ]
        ];
        $httpResponse = [
            'error' => null,
            'result' => $mDeleteResponse
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
        $dataCollection = new Collection($kuzzle, $collection, $index);

        $result = $dataCollection->mDeleteDocument($documentIds['ids'], ['requestId' => $requestId]);

        $this->assertEquals($httpResponse['result'], $result);
    }

    function testMGetDocument()
    {
        $url = KuzzleTest::FAKE_KUZZLE_HOST;
        $requestId = uniqid();
        $index = 'index';
        $collection = 'collection';

        $documentIds = [
            'ids' => ['foo1', 'foo2']
        ];

        $httpRequest = [
            'route' => '/' . $index . '/' . $collection . '/_mGet',
            'method' => 'POST',
            'request' => [
                'volatile' => [],
                'controller' => 'document',
                'action' => 'mGet',
                'body' => $documentIds,
                'requestId' => $requestId,
                'collection' => $collection,
                'index' => $index
            ],
            'query_parameters' => []
        ];
        $mGetResponse = [
            '_source' => [
                'hits' => $documentIds['ids'],
                'total' => count($documentIds['ids'])
            ]
        ];
        $httpResponse = [
            'error' => null,
            'result' => $mGetResponse
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
        $dataCollection = new Collection($kuzzle, $collection, $index);

        $result = $dataCollection->mGetDocument($documentIds['ids'], ['requestId' => $requestId]);

        $this->assertEquals($httpResponse['result'], $result);
    }

    function testMReplaceDocument()
    {
        $url = KuzzleTest::FAKE_KUZZLE_HOST;
        $requestId = uniqid();
        $index = 'index';
        $collection = 'collection';

        $documents = [
            'documents' => [
                ['_id' => 'foo1', 'foo' => 'bar'],
                ['_id' => 'foo2', 'foo' => 'bar'],
            ]
        ];

        $httpRequest = [
            'route' => '/' . $index . '/' . $collection . '/_mReplace',
            'method' => 'PUT',
            'request' => [
                'volatile' => [],
                'controller' => 'document',
                'action' => 'mReplace',
                'body' => $documents,
                'requestId' => $requestId,
                'collection' => $collection,
                'index' => $index
            ],
            'query_parameters' => []
        ];
        $mReplaceResponse = [
            '_source' => [
                'hits' => $documents['documents'],
                'total' => count($documents['documents'])
            ]
        ];
        $httpResponse = [
            'error' => null,
            'result' => $mReplaceResponse
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
        $dataCollection = new Collection($kuzzle, $collection, $index);

        $result = $dataCollection->mReplaceDocument($documents['documents'], ['requestId' => $requestId]);

        $this->assertEquals($httpResponse['result'], $result);
    }

    function testMUpdateDocument()
    {
        $url = KuzzleTest::FAKE_KUZZLE_HOST;
        $requestId = uniqid();
        $index = 'index';
        $collection = 'collection';

        $documents = [
            'documents' => [
                ['_id' => 'foo1', 'foo' => 'bar'],
                ['_id' => 'foo2', 'foo' => 'bar'],
            ]
        ];

        $httpRequest = [
            'route' => '/' . $index . '/' . $collection . '/_mUpdate',
            'method' => 'PUT',
            'request' => [
                'volatile' => [],
                'controller' => 'document',
                'action' => 'mUpdate',
                'body' => $documents,
                'requestId' => $requestId,
                'collection' => $collection,
                'index' => $index
            ],
            'query_parameters' => []
        ];
        $mUpdateResponse = [
            '_source' => [
                'hits' => $documents['documents'],
                'total' => count($documents['documents'])
            ]
        ];
        $httpResponse = [
            'error' => null,
            'result' => $mUpdateResponse
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
        $dataCollection = new Collection($kuzzle, $collection, $index);

        $result = $dataCollection->mUpdateDocument($documents['documents'], ['requestId' => $requestId]);

        $this->assertEquals($httpResponse['result'], $result);
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
            'route' => '/' . $index . '/' . $collection . '/_publish',
            'method' => 'POST',
            'request' => [
                'volatile' => [],
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
        $dataCollection = new Collection($kuzzle, $collection, $index);

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
        $documentContent = [
            'foo' => 'bar'
        ];
        $documentMeta = [
            'author' => 'foo'
        ];

        $httpRequest = [
            'route' => '/' . $index . '/' . $collection . '/_publish',
            'method' => 'POST',
            'request' => [
                'volatile' => [],
                'controller' => 'realtime',
                'action' => 'publish',
                'body' => $documentContent,
                'requestId' => $requestId,
                'collection' => $collection,
                'index' => $index,
                '_id' => $documentId,
                'meta' => $documentMeta
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
        $dataCollection = new Collection($kuzzle, $collection, $index);

        $documentObject = new \Kuzzle\Document($dataCollection, $documentId, $documentContent, $documentMeta);

        $result = $dataCollection->publishMessage($documentObject, ['requestId' => $requestId]);

        $this->assertEquals(true, $result);
    }

    function testScrollSpecifications()
    {
        $url = KuzzleTest::FAKE_KUZZLE_HOST;
        $requestId = uniqid();
        $index = 'index';
        $collection = 'collection';

        $scrollId = '1337';

        $httpRequest = [
            'route' => '/validations/_scroll/' . $scrollId,
            'method' => 'GET',
            'request' => [
                'volatile' => [],
                'controller' => 'collection',
                'action' => 'scrollSpecifications',
                'requestId' => $requestId
            ],
            'query_parameters' => []
        ];
        $scrollSpecificationsResponse = [
            'total' => 2,
            'hits' => [
                [
                    '_id' => 'foo#bar',
                    '_source' => [
                        'validation' => [
                            'strict' => true,
                            'fields' => [
                                'foo' => [
                                    'mandatory' => true,
                                    'type' => 'string',
                                    'defaultValue' => 'bar'
                                ]
                            ]
                        ]
                    ],
                    'index' => 'foo',
                    'collection' => 'bar'
                ]
            ]
        ];

        $httpResponse = [
            'error' => null,
            'result' => $scrollSpecificationsResponse
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

        $dataCollection = new Collection($kuzzle, $collection, $index);

        $result = $dataCollection->scrollSpecifications($scrollId, ['requestId' => $requestId]);

        $this->assertEquals($scrollSpecificationsResponse, $result);
    }

    function testSearchSpecifications()
    {
        $url = KuzzleTest::FAKE_KUZZLE_HOST;
        $requestId = uniqid();
        $index = 'index';
        $collection = 'collection';

        $filters = ['match_all' => ['boost' => 1]];

        $httpRequest = [
            'route' => '/validations/_search',
            'method' => 'POST',
            'request' => [
                'volatile' => [],
                'controller' => 'collection',
                'action' => 'searchSpecifications',
                'requestId' => $requestId,
                'body' => ['query' => $filters]
            ],
            'query_parameters' => []
        ];
        $searchSpecificationsResponse = [
            'total' => 2,
            'hits' => [
                [
                    '_id' => 'foo#bar',
                    '_source' => [
                        'validation' => [
                            'strict' => true,
                            'fields' => [
                                'foo' => [
                                    'mandatory' => true,
                                    'type' => 'string',
                                    'defaultValue' => 'bar'
                                ]
                            ]
                        ]
                    ],
                    'index' => 'foo',
                    'collection' => 'bar'
                ],
                [
                    '_id' => 'bar#foo',
                    '_source' => [
                        'validation' => [
                            'strict' => true,
                            'fields' => [
                                'bar' => [
                                    'mandatory' => true,
                                    'type' => 'string',
                                    'defaultValue' => 'foo'
                                ]
                            ]
                        ]
                    ],
                    'index' => 'bar',
                    'collection' => 'foo'
                ],
            ],
            'scrollId' => '1337'
        ];
        $httpResponse = [
            'error' => null,
            'result' => $searchSpecificationsResponse
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

        $dataCollection = new Collection($kuzzle, $collection, $index);

        $result = $dataCollection->searchSpecifications($filters, ['requestId' => $requestId]);

        $this->assertEquals($searchSpecificationsResponse, $result);
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
                'volatile' => [],
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

        $dataCollection = new Collection($kuzzle, $collection, $index);

        $result = $dataCollection->truncate(['requestId' => $requestId]);

        $this->assertEquals([$documentId], $result);
    }

    function testUpdateSpecifications()
    {
        $url = KuzzleTest::FAKE_KUZZLE_HOST;
        $requestId = uniqid();
        $index = 'index';
        $collection = 'collection';

        $specificationsContent = [
            'strict' => true,
            'fields' => [
                'foo' => [
                    'mandatory' => true,
                    'type' => 'string',
                    'defaultValue' => 'bar'
                ]
            ]
        ];

        $kuzzle = $this
            ->getMockBuilder('\Kuzzle\Kuzzle')
            ->setMethods(['emitRestRequest'])
            ->setConstructorArgs([$url])
            ->getMock();

        $httpUpdateSpecificationsRequest = [
            'route' => '/_specifications',
            'method' => 'PUT',
            'request' => [
                'volatile' => [],
                'controller' => 'collection',
                'action' => 'updateSpecifications',
                'body' => [$index => [$collection => $specificationsContent]],
                'requestId' => $requestId,
                'collection' => $collection,
                'index' => $index
            ],
            'query_parameters' => []
        ];
        $updateSpecificationsResponse = [
            '_source' => $specificationsContent
        ];
        $httpUpdateSpecificationsResponse = [
            'error' => null,
            'result' => $updateSpecificationsResponse
        ];

        $kuzzle
            ->expects($this->once())
            ->method('emitRestRequest')
            ->with($httpUpdateSpecificationsRequest)
            ->willReturn($httpUpdateSpecificationsResponse);

        /**
         * @var Kuzzle $kuzzle
         */
        $dataCollection = new Collection($kuzzle, $collection, $index);

        $response = $dataCollection->updateSpecifications($specificationsContent, ['requestId' => $requestId]);

        $this->assertEquals($response, $httpUpdateSpecificationsResponse['result']);
    }

    function testValidateSpecifications()
    {
        $url = KuzzleTest::FAKE_KUZZLE_HOST;
        $requestId = uniqid();
        $index = 'index';
        $collection = 'collection';

        $specificationsContent = [
            'strict' => true,
            'fields' => [
                'foo' => [
                    'mandatory' => true,
                    'type' => 'string',
                    'defaultValue' => 'bar'
                ]
            ]
        ];

        $kuzzle = $this
            ->getMockBuilder('\Kuzzle\Kuzzle')
            ->setMethods(['emitRestRequest'])
            ->setConstructorArgs([$url])
            ->getMock();

        $httpValidateUpdateSpecificationsRequest = [
            'route' => '/_validateSpecifications',
            'method' => 'POST',
            'request' => [
                'volatile' => [],
                'controller' => 'collection',
                'action' => 'validateSpecifications',
                'body' => [$index => [$collection => $specificationsContent]],
                'requestId' => $requestId,
                'collection' => $collection,
                'index' => $index
            ],
            'query_parameters' => []
        ];
        $validateSpecificationsResponse = [
            'valid' => true
        ];
        $httpValidateSpecificationsResponse = [
            'error' => null,
            'result' => $validateSpecificationsResponse
        ];

        $kuzzle
            ->expects($this->once())
            ->method('emitRestRequest')
            ->with($httpValidateUpdateSpecificationsRequest)
            ->willReturn($httpValidateSpecificationsResponse);

        /**
         * @var Kuzzle $kuzzle
         */
        $dataCollection = new Collection($kuzzle, $collection, $index);

        $response = $dataCollection->validateSpecifications($specificationsContent, ['requestId' => $requestId]);

        $this->assertEquals($response, $httpValidateSpecificationsResponse['result']['valid']);
    }
}
