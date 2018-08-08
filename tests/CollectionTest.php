<?php

use Kuzzle\Util\CurlRequest;

class CollectionTest extends \PHPUnit_Framework_TestCase
{
    const FAKE_KUZZLE_HOST = '127.0.0.1';
    const FAKE_KUZZLE_URL = 'http://127.0.0.1:7512';

    function testCreate()
    {
        $url = self::FAKE_KUZZLE_HOST;

        $kuzzle = $this
            ->getMockBuilder('\Kuzzle\Kuzzle')
            ->setMethods(['emitRestRequest'])
            ->setConstructorArgs([$url])
            ->getMock();

        $requestId = uniqid();
        $index = 'index';
        $collection = 'collection';
        $mapping = ['foo' => ['type' => 'keyword']];

        $httpRequest = [
            'route' => '/' . $index . '/' . $collection ,
            'method' => 'PUT',
            'request' => [
                'volatile' => [],
                'controller' => 'collection',
                'action' => 'create',
                'index' => $index,
                'collection' => $collection,
                'requestId' => $requestId,
                'body' => json_encode($mapping)
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

        $kuzzle
            ->expects($this->once())
            ->method('emitRestRequest')
            ->with($httpRequest)
            ->willReturn($httpResponse);

        /**
         * @var Kuzzle $kuzzle
         */
        $response = $kuzzle->collection->create($index, $collection, ['foo' => ['type' => 'keyword']], ['requestId' => $requestId]);

        $this->assertEquals(true, $response);
    }

    function testCreateWithNoIndexName()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->collection->create('', 'collection', ['foo' => ['type' => 'keyword']], []);

            $this->fail('KuzzleTest::testCreateWithNoIndexName => Should raise an exception (could not be called without index nor collection)');
        } catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Collection::create: index and collection name required', $e->getMessage());
        }
    }

    function testCreateWithNoCollectionName()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->collection->create('index', '', ['foo' => ['type' => 'keyword']], []);

            $this->fail('KuzzleTest::testCreateWithNoCollectionName => Should raise an exception (could not be called without index nor collection)');
        } catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Collection::create: index and collection name required', $e->getMessage());
        }
    }

    function testExists()
    {
        $url = self::FAKE_KUZZLE_HOST;

        $kuzzle = $this
            ->getMockBuilder('\Kuzzle\Kuzzle')
            ->setMethods(['emitRestRequest'])
            ->setConstructorArgs([$url])
            ->getMock();

        $requestId = uniqid();
        $index = 'index';
        $collection = 'collection';

        $httpRequest = [
            'route' => '/' . $index . '/' . $collection . '/_exists',
            'method' => 'GET',
            'request' => [
                'volatile' => [],
                'controller' => 'collection',
                'action' => 'exists',
                'index' => $index,
                'collection' => $collection,
                'requestId' => $requestId,
            ],
            'query_parameters' => []
        ];
        $existsCollectionResponse = true;
        $httpResponse = [
            'error' => null,
            'result' => $existsCollectionResponse
        ];

        $kuzzle
            ->expects($this->once())
            ->method('emitRestRequest')
            ->with($httpRequest)
            ->willReturn($httpResponse);

        /**
         * @var Kuzzle $kuzzle
         */
        $response = $kuzzle->collection->exists($index, $collection, ['requestId' => $requestId]);

        $this->assertEquals($existsCollectionResponse, $response);
    }

    function testExistsWithNoIndexName()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->collection->exists('', 'collection', []);

            $this->fail('KuzzleTest::testExistsWithNoIndexName => Should raise an exception (could not be called without index nor collection)');
        } catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Collection::exists: index and collection name required', $e->getMessage());
        }
    }

    function testExistsWithNoCollectionName()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->collection->exists('index', '', []);

            $this->fail('KuzzleTest::testExistsWithNoCollectionName => Should raise an exception (could not be called without index nor collection)');
        } catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Collection::exists: index and collection name required', $e->getMessage());
        }
    }

    public function testListAllCollections()
    {
        $url = self::FAKE_KUZZLE_HOST;
        $index = 'index';
        $from = 0;
        $size = 42;
        $collectionType = 'all';

        $kuzzle = $this
            ->getMockBuilder('\Kuzzle\Kuzzle')
            ->setMethods(['emitRestRequest'])
            ->setConstructorArgs([$url])
            ->getMock();

        $options = [
            'requestId' => uniqid(),
            'from' => $from,
            'size' => $size
        ];

        // mock http request
        $httpRequest = [
            'route' => '/' . $index . '/_list',
            'request' => [
                'action' => 'list',
                'controller' => 'collection',
                'index' => $index,
                'volatile' => [],
                'requestId' => $options['requestId']
            ],
            'method' => 'GET',
            'query_parameters' => [
                'from' => $from,
                'size' => $size
            ]
        ];

        // mock response
        $listCollectionsResponse = ['collections' => []];
        $httpResponse = [
            'error' => null,
            'result' => $listCollectionsResponse
        ];

        $kuzzle
            ->expects($this->once())
            ->method('emitRestRequest')
            ->with($httpRequest)
            ->willReturn($httpResponse);

        /**
         * @var \Kuzzle\Kuzzle $kuzzle
         */
        $response = $kuzzle->collection->listCollections($index, $options);

        $this->assertEquals($listCollectionsResponse['collections'], $response);
    }

    public function testListRealtimeCollections()
    {
        $url = self::FAKE_KUZZLE_HOST;
        $index = 'index';
        $collectionType = 'realtime';

        $kuzzle = $this
            ->getMockBuilder('\Kuzzle\Kuzzle')
            ->setMethods(['emitRestRequest'])
            ->setConstructorArgs([$url])
            ->getMock();

        $options = [
            'type' => $collectionType,
            'requestId' => uniqid()
        ];

        // mock http request
        $httpRequest = [
            'route' => '/' . $index . '/_list',
            'request' => [
                'action' => 'list',
                'controller' => 'collection',
                'index' => $index,
                'volatile' => [],
                'requestId' => $options['requestId']
            ],
            'method' => 'GET',
            'query_parameters' => []
        ];

        // mock response
        $listCollectionsResponse = ['collections' => []];
        $httpResponse = [
            'error' => null,
            'result' => $listCollectionsResponse
        ];

        $kuzzle
            ->expects($this->once())
            ->method('emitRestRequest')
            ->with($httpRequest)
            ->willReturn($httpResponse);

        /**
         * @var \Kuzzle\Kuzzle $kuzzle
         */
        $response = $kuzzle->collection->listCollections($index, $options);

        $this->assertEquals($listCollectionsResponse['collections'], $response);
    }

    public function testListCollectionsWithoutIndex()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->collection->listCollections('', []);

            $this->fail('KuzzleTest::testListCollectionsWithoutIndex => Should raise an exception (could not be called without index nor default index)');
        }
        catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Collection::listCollections: index name required', $e->getMessage());
        }
    }

    function testTruncate()
    {
        $url = self::FAKE_KUZZLE_HOST;
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
                'index' => $index,
                'collection' => $collection,
                'requestId' => $requestId,
            ],
            'query_parameters' => []
        ];
        $truncateCollectionResponse = [
            'acknowledged' => true
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

        $response = $kuzzle->collection->truncate($index, $collection, ['requestId' => $requestId]);

        $this->assertEquals(true, $response);
    }

    public function testTruncateWithoutIndex()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->collection->truncate('', 'collection', []);

            $this->fail('KuzzleTest::testTruncateWithoutIndex => Should raise an exception (could not be called without index nor default index)');
        }
        catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Collection::truncate: index and collection name required', $e->getMessage());
        }
    }

    function testTruncateWithNoCollectionName()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->collection->truncate('index', '', []);

            $this->fail('KuzzleTest::testTruncateWithNoCollectionName => Should raise an exception (could not be called without index nor collection)');
        } catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Collection::truncate: index and collection name required', $e->getMessage());
        }
    }

    function testGetSpecifications()
    {
        $url = self::FAKE_KUZZLE_HOST;
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
                'index' => $index,
                'collection' => $collection,
                'requestId' => $requestId,
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

        $response = $kuzzle->collection->getSpecifications($index, $collection, ['requestId' => $requestId]);

        $this->assertEquals($specificationsContent, $response['_source']);
    }

    public function testGetSpecificationsWithoutIndex()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->collection->getSpecifications('', 'collection', []);

            $this->fail('KuzzleTest::testGetSpecificationsWithoutIndex => Should raise an exception (could not be called without index nor default index)');
        }
        catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Collection::getSpecifications: index and collection name required', $e->getMessage());
        }
    }

    function testGetSpecificationsWithNoCollectionName()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->collection->getSpecifications('index', '', []);

            $this->fail('KuzzleTest::testGetSpecificationsWithNoCollectionName => Should raise an exception (could not be called without index nor collection)');
        } catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Collection::getSpecifications: index and collection name required', $e->getMessage());
        }
    }

    function testDeleteSpecifications()
    {
        $url = self::FAKE_KUZZLE_HOST;
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
            'method' => 'DELETE',
            'request' => [
                'volatile' => [],
                'controller' => 'collection',
                'action' => 'deleteSpecifications',
                'index' => $index,
                'collection' => $collection,
                'requestId' => $requestId,
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

        $response = $kuzzle->collection->deleteSpecifications($index, $collection, ['requestId' => $requestId]);

        $this->assertEquals($specificationsContent, $response['_source']);
    }

    public function testDeleteSpecificationsWithoutIndex()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->collection->deleteSpecifications('', 'collection', []);

            $this->fail('KuzzleTest::testDeleteSpecificationsWithoutIndex => Should raise an exception (could not be called without index nor default index)');
        }
        catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Collection::deleteSpecifications: index and collection name required', $e->getMessage());
        }
    }

    function testDeleteSpecificationsWithNoCollectionName()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->collection->deleteSpecifications('index', '', []);

            $this->fail('KuzzleTest::testDeleteSpecificationsWithNoCollectionName => Should raise an exception (could not be called without index nor collection)');
        } catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Collection::deleteSpecifications: index and collection name required', $e->getMessage());
        }
    }

    function testScrollSpecifications()
    {
        $url = self::FAKE_KUZZLE_HOST;
        $requestId = uniqid();

        $scrollId = '1337';

        $httpRequest = [
            'route' => '/validations/_scroll/' . $scrollId,
            'method' => 'GET',
            'request' => [
                'volatile' => [],
                'controller' => 'collection',
                'action' => 'scrollSpecifications',
                'scrollId' => $scrollId,
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

        $response = $kuzzle->collection->scrollSpecifications($scrollId, ['requestId' => $requestId]);

        $this->assertEquals($scrollSpecificationsResponse, $response);
    }

    function testScrollSpecificationsWithNoScrollId()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->collection->scrollSpecifications('', []);

            $this->fail('KuzzleTest::testScrollSpecificationsWithNoScrollId => Should raise an exception (could not be called without index nor collection)');
        } catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Collection::scrollSpecifications: scrollId is required', $e->getMessage());
        }
    }

    function testSearchSpecifications()
    {
        $url = self::FAKE_KUZZLE_HOST;
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
                'body' => ['query' => json_encode($filters)]
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

        $reponse = $kuzzle->collection->searchSpecifications($filters, ['requestId' => $requestId]);

        $this->assertEquals($searchSpecificationsResponse, $reponse);
    }

    function testUpdateSpecifications()
    {
        $url = self::FAKE_KUZZLE_HOST;
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
                'body' => [$index => [$collection => json_encode($specificationsContent)]],
                'requestId' => $requestId
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

        $response = $kuzzle->collection->updateSpecifications($index, $collection, $specificationsContent, ['requestId' => $requestId]);

        $this->assertEquals($response, $httpUpdateSpecificationsResponse['result']);
    }

    function testUpdateSpecificationsWithNoIndex()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->collection->updateSpecifications('', 'collection', ['specs' => 'specDetailled'], []);

            $this->fail('KuzzleTest::testUpdateSpecificationsWithNoIndex => Should raise an exception (could not be called without index nor collection)');
        } catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Collection::updateSpecifications: index name, collection name and specifications as an array are required', $e->getMessage());
        }
    }

    function testUpdateSpecificationsWithNoCollection()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->collection->updateSpecifications('index', '', ['specs' => 'specDetailled'], []);

            $this->fail('KuzzleTest::testUpdateSpecificationsWithNoCollection => Should raise an exception (could not be called without index nor collection)');
        } catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Collection::updateSpecifications: index name, collection name and specifications as an array are required', $e->getMessage());
        }
    }

    function testUpdateSpecificationsWithBadSpecs()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->collection->updateSpecifications('index', 'collection', [], []);

            $this->fail('KuzzleTest::testUpdateSpecificationsWithNoSpecs => Should raise an exception (could not be called without index nor collection)');
        } catch (Exception $e) {
            $this->assertInstanceOf('ErrorException', $e);
        }
    }

    function testValidateSpecifications()
    {
        $url = self::FAKE_KUZZLE_HOST;
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
                'body' => [$index => [$collection => json_encode($specificationsContent)]],
                'requestId' => $requestId,
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

        $response = $kuzzle->collection->validateSpecifications($index, $collection, $specificationsContent, ['requestId' => $requestId]);

        $this->assertEquals($response, $httpValidateSpecificationsResponse['result']['valid']);
    }

    function testValidateSpecificationsWithNoIndex()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->collection->validateSpecifications('', 'collection', [], []);

            $this->fail('KuzzleTest::testValidateSpecificationsWithNoIndex => Should raise an exception (could not be called without index nor collection)');
        } catch (Exception $e) {
          $this->assertInstanceOf('InvalidArgumentException', $e);
          $this->assertEquals('Kuzzle\Collection::validateSpecifications: index name, collection name and specifications as an array are required', $e->getMessage());
        }
    }

    function testValidateSpecificationsWithNoCollection()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->collection->validateSpecifications('index', '', [], []);

            $this->fail('KuzzleTest::testValidateSpecificationsWithNoCollection => Should raise an exception (could not be called without index nor collection)');
        } catch (Exception $e) {
          $this->assertInstanceOf('InvalidArgumentException', $e);
          $this->assertEquals('Kuzzle\Collection::validateSpecifications: index name, collection name and specifications as an array are required', $e->getMessage());
        }
    }

    function testValidateSpecificationsWithBadSpecs()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->collection->validateSpecifications('index', 'collection', [], []);

            $this->fail('KuzzleTest::testValidateSpecificationsWithBadSpecs => Should raise an exception (could not be called without index nor collection)');
        } catch (Exception $e) {
            $this->assertInstanceOf('ErrorException', $e);
        }
    }

    function testGetMapping()
    {
        $url = self::FAKE_KUZZLE_HOST;
        $requestId = uniqid();
        $index = 'index';
        $collection = 'collection';

        $httpRequest = [
            'route' => '/' . $index . '/' . $collection . '/_mapping',
            'method' => 'GET',
            'request' => [
                'volatile' => [],
                'controller' => 'collection',
                'action' => 'getMapping',
                'index' => $index,
                'collection' => $collection,
                'requestId' => $requestId,
            ],
            'query_parameters' => []
        ];

        $getMappingResponse = [
            'index' => [
                'mappings' => [
                    'collection' => [
                        'properties' => [
                          'field1' => [
                              'type' => "field type", "...options..."
                          ]
                        ]
                    ]
                ]
            ]
        ];

        $httpResponse = [
            'error' => null,
            'result' => $getMappingResponse
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

        $response = $kuzzle->collection->getMapping($index, $collection, ['requestId' => $requestId]);
        $this->assertEquals($getMappingResponse, $response);
    }

    public function testGetMappingWithoutIndex()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->collection->getMapping('', 'collection', []);

            $this->fail('KuzzleTest::testGetMappingWithoutIndex => Should raise an exception (could not be called without index nor default index)');
        }
        catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Collection::getMapping: index and collection name required', $e->getMessage());
        }
    }

    function testGetMappingWithNoCollectionName()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->collection->getMapping('index', '', []);

            $this->fail('KuzzleTest::testGetMappingWithNoCollectionName => Should raise an exception (could not be called without index nor collection)');
        } catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Collection::getMapping: index and collection name required', $e->getMessage());
        }
    }

    function testUpdateMapping()
    {
        $url = self::FAKE_KUZZLE_HOST;
        $requestId = uniqid();
        $index = 'index';
        $collection = 'collection';

        $updateMappingBody = [
            'index' => [
                'mappings' => [
                    'collection' => [
                        'properties' => [
                          'field1' => [
                              'type' => "field type", "...options..."
                          ]
                        ]
                    ]
                ]
            ]
        ];

        $httpRequest = [
            'route' => '/' . $index . '/' . $collection . '/_mapping',
            'method' => 'PUT',
            'request' => [
                'volatile' => [],
                'controller' => 'collection',
                'action' => 'updateMapping',
                'index' => $index,
                'collection' => $collection,
                'body' => [
                    'properties' => json_encode($updateMappingBody)
                ],
                'requestId' => $requestId,
            ],
            'query_parameters' => []
        ];

        $httpResponse = [
            'error' => null,
            'result' => []
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

        $response = $kuzzle->collection->updateMapping($index, $collection, $updateMappingBody, ['requestId' => $requestId]);
        $this->assertEquals([], $response);
    }

    public function testUpdateMappingWithoutIndex()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->collection->updateMapping('', 'collection', [], []);

            $this->fail('KuzzleTest::testUpdateMappingWithoutIndex => Should raise an exception (could not be called without index nor default index)');
        }
        catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Collection::updateMapping: index name, collection name and mapping as an array are required', $e->getMessage());
        }
    }

    function testUpdateMappingWithNoCollectionName()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->collection->updateMapping('index', '', [], []);

            $this->fail('KuzzleTest::testUpdateMappingWithNoCollectionName => Should raise an exception (could not be called without index nor collection)');
        } catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Collection::updateMapping: index name, collection name and mapping as an array are required', $e->getMessage());
        }
    }
}
