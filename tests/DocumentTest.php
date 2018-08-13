<?php

use Kuzzle\Document;
use Kuzzle\Collection;
use Kuzzle\Kuzzle;

class DocumentTest extends \PHPUnit_Framework_TestCase
{
    const FAKE_KUZZLE_HOST = '127.0.0.1';
    const FAKE_KUZZLE_URL = 'http://127.0.0.1:7512';

    function testCreate()
    {
        $url = self::FAKE_KUZZLE_HOST;
        $requestId = uniqid();
        $index = 'index';
        $collection = 'collection';
        $documentId = uniqid();
        $documentContent = [
            'foo' => 'bar'
        ];

        $httpRequest = [
            'route' => '/' . $index . '/' . $collection . '/' . $documentId . '/_create',
            'method' => 'POST',
            'request' => [
                'volatile' => [],
                'controller' => 'document',
                'action' => 'create',
                'requestId' => $requestId,
                'collection' => $collection,
                'index' => $index,
                '_id' => $documentId,
                'body' => json_encode($documentContent)
            ],
            'query_parameters' => []
        ];
        $createDocumentResponse = [
            '_id' => $documentId,
            '_source' => $documentContent,
            '_meta' => null,
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

        $result = $kuzzle->document->create($index, $collection, $documentId, $documentContent, ['requestId' => $requestId]);

        $this->assertEquals($documentId, $result['_id']);
    }

    function testCreateWithNoIndexName()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->document->create('', 'collection', uniqid(), ['content' => 'someContent'], []);

            $this->fail('KuzzleTest::testCreateWithNoIndexName => Should raise an exception (could not be called without index nor collection)');
        } catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Document::create: cannot create a document without an index, a collection, a document ID and a document body', $e->getMessage());
        }
    }

    function testCreateWithNoCollectionName()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->document->create('index', '', uniqid(), ['content' => 'someContent'], []);

            $this->fail('KuzzleTest::testCreateWithNoCollectionName => Should raise an exception (could not be called without index nor collection)');
        } catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Document::create: cannot create a document without an index, a collection, a document ID and a document body', $e->getMessage());
        }
    }

    function testCreateWithNoDocumentID()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->document->create('index', 'collection', '', ['content' => 'someContent'], []);

            $this->fail('KuzzleTest::testCreateWithNoDocumentID => Should raise an exception (could not be called without index nor collection)');
        } catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Document::create: cannot create a document without an index, a collection, a document ID and a document body', $e->getMessage());
        }
    }

    function testCreateWithNoDocumentContent()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->document->create('index', 'collection', uniqid(), [], []);

            $this->fail('KuzzleTest::testCreateWithNoDocumentID => Should raise an exception (could not be called without index nor collection)');
        } catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Document::create: cannot create a document without an index, a collection, a document ID and a document body', $e->getMessage());
        }
    }

    function testDelete()
    {
        $url = self::FAKE_KUZZLE_HOST;
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
                '_id' => $documentId,
            ],
            'query_parameters' => []
        ];
        $deleteResponse = [
            '_id' => $documentId
        ];
        $httpResponse = [
            'error' => null,
            'result' => $deleteResponse
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

        $result = $kuzzle->document->delete($index, $collection, $documentId, ['requestId' => $requestId]);

        $this->assertEquals($documentId, $result['_id']);
    }

    function testDeleteWithNoIndexName()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->document->delete('', 'collection', uniqid(), []);

            $this->fail('KuzzleTest::testDeleteWithNoIndexName => Should raise an exception (could not be called without index nor collection)');
        } catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Document::delete: cannot delete a document without an index, a collection and a document ID', $e->getMessage());
        }
    }

    function testDeleteWithNoCollectionName()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->document->delete('index', '', uniqid(), []);

            $this->fail('KuzzleTest::testDeleteWithNoCollectionName => Should raise an exception (could not be called without index nor collection)');
        } catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Document::delete: cannot delete a document without an index, a collection and a document ID', $e->getMessage());
        }
    }

    function testDeleteWithNoDocumentID()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->document->delete('index', 'collection', '', []);

            $this->fail('KuzzleTest::testDeleteWithNoDocumentID => Should raise an exception (could not be called without index nor collection)');
        } catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Document::delete: cannot delete a document without an index, a collection and a document ID', $e->getMessage());
        }
    }

    function testDeleteByQuery()
    {
        $url = self::FAKE_KUZZLE_HOST;
        $requestId = uniqid();
        $index = 'index';
        $collection = 'collection';
        $query = ['query' => 'query content'];

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
                'body' => ['query' => json_encode($query)]
            ],
            'query_parameters' => []
        ];
        $deleteResponse = [
            'hits' => ['id']
        ];
        $httpResponse = [
            'error' => null,
            'result' => $deleteResponse
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

        $result = $kuzzle->document->deleteByQuery($index, $collection, $query, ['requestId' => $requestId]);

        $this->assertEquals($deleteResponse['hits'], $result);
    }

    function testDeleteByQueryWithNoIndexName()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->document->deleteByQuery('', 'collection',  ['query' => 'query content'], []);

            $this->fail('KuzzleTest::testDeleteByQueryWithNoIndexName => Should raise an exception (could not be called without index nor collection)');
        } catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Document::deleteByQuery: cannot delete a document by query without an index, a collection and a query', $e->getMessage());
        }
    }

    function testDeleteByQueryWithNoCollectionName()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->document->deleteByQuery('index', '', ['query' => 'query content'], []);

            $this->fail('KuzzleTest::testDeleteByQueryWithNoCollectionName => Should raise an exception (could not be called without index nor collection)');
        } catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Document::deleteByQuery: cannot delete a document by query without an index, a collection and a query', $e->getMessage());
        }
    }

    function testDeleteByQueryWithNoQuery()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->document->deleteByQuery('index', 'collection', [], []);

            $this->fail('KuzzleTest::testDeleteByQueryWithNoDocumentID => Should raise an exception (could not be called without index nor collection)');
        } catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Document::deleteByQuery: cannot delete a document by query without an index, a collection and a query', $e->getMessage());
        }
    }

    function testExists()
    {
        $url = self::FAKE_KUZZLE_HOST;
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
                '_id' => $documentId,
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

        $result = $kuzzle->document->exists($index, $collection, $documentId, ['requestId' => $requestId]);

        $this->assertEquals(true, $result);
    }

    function testExistsWithNoIndexName()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->document->exists('', 'collection', uniqid(), []);

            $this->fail('KuzzleTest::testExistsWithNoIndexName => Should raise an exception (could not be called without index nor collection)');
        } catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Document::exists: cannot check if a document exists without an index, a collection and a document ID', $e->getMessage());
        }
    }

    function testExistsWithNoCollectionName()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->document->exists('index', '', uniqid(), []);

            $this->fail('KuzzleTest::testExistsWithNoCollectionName => Should raise an exception (could not be called without index nor collection)');
        } catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Document::exists: cannot check if a document exists without an index, a collection and a document ID', $e->getMessage());
        }
    }

    function testExistsWithNoDocumentID()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->document->exists('index', 'collection', '', []);

            $this->fail('KuzzleTest::testExistsWithNoDocumentID => Should raise an exception (could not be called without index nor collection)');
        } catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Document::exists: cannot check if a document exists without an index, a collection and a document ID', $e->getMessage());
        }
    }

    function testGet()
    {
        $url = self::FAKE_KUZZLE_HOST;
        $requestId = uniqid();
        $index = 'index';
        $collection = 'collection';
        $documentId = uniqid();

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
                '_id' => $documentId,
            ],
            'query_parameters' => []
        ];

        $getResponse = [
            '_id' => $documentId,
            '_source' => '',
            '_meta' => null,
            '_version' => 1
        ];

        $httpResponse = [
            'error' => null,
            'result' => $getResponse
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

        $result = $kuzzle->document->get($index, $collection, $documentId, ['requestId' => $requestId]);

        $this->assertEquals($getResponse, $result);
    }

    function testGetWithNoIndexName()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->document->get('', 'collection', uniqid(), []);

            $this->fail('KuzzleTest::testGetWithNoIndexName => Should raise an exception (could not be called without index nor collection)');
        } catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Document::get: cannot retrieve a document without an index, a collection and a document ID', $e->getMessage());
        }
    }

    function testGetWithNoCollectionName()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->document->get('index', '', uniqid(), []);

            $this->fail('KuzzleTest::testGetWithNoCollectionName => Should raise an exception (could not be called without index nor collection)');
        } catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Document::get: cannot retrieve a document without an index, a collection and a document ID', $e->getMessage());
        }
    }

    function testGetWithNoDocumentID()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->document->get('index', 'collection', '', []);

            $this->fail('KuzzleTest::testGetWithNoDocumentID => Should raise an exception (could not be called without index nor collection)');
        } catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Document::get: cannot retrieve a document without an index, a collection and a document ID', $e->getMessage());
        }
    }

    function testCount()
    {
        $url = self::FAKE_KUZZLE_HOST;
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
                'body' => ['filters' => json_encode($filter)],
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

        $count = $kuzzle->document->count($index, $collection, $filter, ['requestId' => $requestId]);

        $this->assertEquals(2, $count);
    }

    function testCountWithNoIndexName()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->document->get('', 'collection', uniqid(), []);

            $this->fail('KuzzleTest::testCountWithNoIndexName => Should raise an exception (could not be called without index nor collection)');
        } catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Document::get: cannot retrieve a document without an index, a collection and a document ID', $e->getMessage());
        }
    }

    function testCountWithNoCollectionName()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->document->get('index', '', uniqid(), []);

            $this->fail('KuzzleTest::testCountWithNoCollectionName => Should raise an exception (could not be called without index nor collection)');
        } catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Document::get: cannot retrieve a document without an index, a collection and a document ID', $e->getMessage());
        }
    }

    function testReplace()
    {
        $url = self::FAKE_KUZZLE_HOST;
        $requestId = uniqid();
        $index = 'index';
        $collection = 'collection';
        $documentId = uniqid();
        $documentContent = [
            'foo' => 'bar'
        ];

        $httpRequest = [
            'route' => '/' . $index . '/' . $collection . '/' . $documentId . '/_replace',
            'method' => 'PUT',
            'request' => [
                'volatile' => [],
                'controller' => 'document',
                'action' => 'replace',
                'requestId' => $requestId,
                'collection' => $collection,
                'index' => $index,
                '_id' => $documentId,
                'body' => json_encode($documentContent)
            ],
            'query_parameters' => []
        ];
        $replaceDocumentResponse = [
            '_id' => $documentId,
            '_source' => $documentContent,
            '_meta' => null,
            '_version' => 1
        ];

        $httpResponse = [
            'error' => null,
            'result' => $replaceDocumentResponse
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

        $result = $kuzzle->document->replace($index, $collection, $documentId, $documentContent, ['requestId' => $requestId]);

        $this->assertEquals($documentId, $result['_id']);
    }

    function testReplaceWithNoIndexName()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->document->replace('', 'collection', uniqid(), ['content' => 'someContent'], []);

            $this->fail('KuzzleTest::testReplaceWithNoIndexName => Should raise an exception (could not be called without index nor collection)');
        } catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Document::replace: cannot replace a document without an index, a collection, a document ID and a document body', $e->getMessage());
        }
    }

    function testReplaceWithNoCollectionName()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->document->replace('index', '', uniqid(), ['content' => 'someContent'], []);

            $this->fail('KuzzleTest::testReplaceWithNoCollectionName => Should raise an exception (could not be called without index nor collection)');
        } catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Document::replace: cannot replace a document without an index, a collection, a document ID and a document body', $e->getMessage());
        }
    }

    function testReplaceWithNoDocumentID()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->document->replace('index', 'collection', '', ['content' => 'someContent'], []);

            $this->fail('KuzzleTest::testReplaceWithNoDocumentID => Should raise an exception (could not be called without index nor collection)');
        } catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Document::replace: cannot replace a document without an index, a collection, a document ID and a document body', $e->getMessage());
        }
    }

    function testReplaceWithNoDocumentContent()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->document->replace('index', 'collection', uniqid(), [], []);

            $this->fail('KuzzleTest::testReplaceWithNoDocumentID => Should raise an exception (could not be called without index nor collection)');
        } catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Document::replace: cannot replace a document without an index, a collection, a document ID and a document body', $e->getMessage());
        }
    }

    function testUpdate()
    {
        $url = self::FAKE_KUZZLE_HOST;
        $requestId = uniqid();
        $index = 'index';
        $collection = 'collection';
        $documentId = uniqid();
        $documentContent = [
            'foo' => 'bar'
        ];

        $httpRequest = [
            'route' => '/' . $index . '/' . $collection . '/' . $documentId . '/_update',
            'method' => 'PUT',
            'request' => [
                'volatile' => [],
                'controller' => 'document',
                'action' => 'update',
                'requestId' => $requestId,
                'collection' => $collection,
                'index' => $index,
                '_id' => $documentId,
                'body' => json_encode($documentContent)
            ],
            'query_parameters' => []
        ];
        $updateDocumentResponse = [
            '_id' => $documentId,
            '_source' => $documentContent,
            '_meta' => null,
            '_version' => 1
        ];

        $httpResponse = [
            'error' => null,
            'result' => $updateDocumentResponse
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

        $result = $kuzzle->document->update($index, $collection, $documentId, $documentContent, ['requestId' => $requestId]);

        $this->assertEquals($documentId, $result['_id']);
    }

    function testUpdateWithNoIndexName()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->document->update('', 'collection', uniqid(), ['content' => 'someContent'], []);

            $this->fail('KuzzleTest::testUpdateWithNoIndexName => Should raise an exception (could not be called without index nor collection)');
        } catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Document::update: cannot update a document without an index, a collection, a document ID and a document body', $e->getMessage());
        }
    }

    function testUpdateWithNoCollectionName()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->document->update('index', '', uniqid(), ['content' => 'someContent'], []);

            $this->fail('KuzzleTest::testUpdateWithNoCollectionName => Should raise an exception (could not be called without index nor collection)');
        } catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Document::update: cannot update a document without an index, a collection, a document ID and a document body', $e->getMessage());
        }
    }

    function testUpdateWithNoDocumentID()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->document->update('index', 'collection', '', ['content' => 'someContent'], []);

            $this->fail('KuzzleTest::testUpdateWithNoDocumentID => Should raise an exception (could not be called without index nor collection)');
        } catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Document::update: cannot update a document without an index, a collection, a document ID and a document body', $e->getMessage());
        }
    }

    function testUpdateWithNoDocumentContent()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->document->update('index', 'collection', uniqid(), [], []);

            $this->fail('KuzzleTest::testUpdateWithNoDocumentID => Should raise an exception (could not be called without index nor collection)');
        } catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Document::update: cannot update a document without an index, a collection, a document ID and a document body', $e->getMessage());
        }
    }

    function testValidate()
    {
        $url = self::FAKE_KUZZLE_HOST;
        $requestId = uniqid();
        $index = 'index';
        $collection = 'collection';
        $documentId = uniqid();
        $documentContent = [
            'foo' => 'bar'
        ];

        $httpRequest = [
            'route' => '/' . $index . '/' . $collection . '/_validate',
            'method' => 'POST',
            'request' => [
                'volatile' => [],
                'controller' => 'document',
                'action' => 'validate',
                'requestId' => $requestId,
                'collection' => $collection,
                'index' => $index,
                'body' => json_encode($documentContent)
            ],
            'query_parameters' => []
        ];
        $validateDocumentResponse = [
            'errorMessages' => null,
            'valid' => true
        ];

        $httpResponse = [
            'error' => null,
            'result' => $validateDocumentResponse
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

        $result = $kuzzle->document->validate($index, $collection, $documentContent, ['requestId' => $requestId]);

        $this->assertEquals(true, $result['valid']);
    }

    function testValidateWithNoIndexName()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->document->validate('', 'collection', ['content' => 'someContent'], []);

            $this->fail('KuzzleTest::testValidateWithNoIndexName => Should raise an exception (could not be called without index nor collection)');
        } catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Document::validate: cannot update a document without an index, a collection and a document body', $e->getMessage());
        }
    }

    function testValidateWithNoCollectionName()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->document->validate('index', '', ['content' => 'someContent'], []);

            $this->fail('KuzzleTest::testValidateWithNoCollectionName => Should raise an exception (could not be called without index nor collection)');
        } catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Document::validate: cannot update a document without an index, a collection and a document body', $e->getMessage());
        }
    }

    function testValidateWithNoDocumentContent()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->document->validate('index', 'collection', [], []);

            $this->fail('KuzzleTest::testValidateWithNoDocumentID => Should raise an exception (could not be called without index nor collection)');
        } catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Document::validate: cannot update a document without an index, a collection and a document body', $e->getMessage());
        }
    }


    function testSearch()
    {
        $url = self::FAKE_KUZZLE_HOST;
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
                'body' => json_encode($filter),
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

        $searchResult = $kuzzle->document->search($index, $collection, $filter, $options);

        $this->assertInstanceOf('Kuzzle\Util\SearchResult', $searchResult);
        $this->assertEquals(2, $searchResult->getTotal());
        $this->assertEquals($options, $searchResult->getOptions());
        $this->assertEquals($filter, $searchResult->getFilters());
        $this->assertEquals(2, $searchResult->getFetchedDocuments());

        $documents = $searchResult->getDocuments();
        $this->assertEquals('test', $documents[0]['_id']);
        $this->assertEquals('test1', $documents[1]['_id']);

        $this->assertEquals([
            'aggs_name' => []
        ], $searchResult->getAggregations());
    }

    function testSearchWithNoIndexName()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->document->search('', 'collection', [], []);

            $this->fail('KuzzleTest::testSearchWithNoIndexName => Should raise an exception (could not be called without index nor collection)');
        } catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Document::search: cannot search documents without an index and a collection', $e->getMessage());
        }
    }

    function testSearchWithNoCollectionName()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->document->search('index', '', [], []);

            $this->fail('KuzzleTest::testSearchWithNoCollectionName => Should raise an exception (could not be called without index nor collection)');
        } catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Document::search: cannot search documents without an index and a collection', $e->getMessage());
        }
    }

    function testMCreate()
    {
        $url = self::FAKE_KUZZLE_HOST;
        $requestId = uniqid();
        $index = 'index';
        $collection = 'collection';

        $documents = [
            ['_id' => 'foo1', 'foo' => 'bar'],
            ['_id' => 'foo2', 'foo' => 'bar'],
        ];

        $httpRequest = [
            'route' => '/' . $index . '/' . $collection . '/_mCreate',
            'method' => 'POST',
            'request' => [
                'volatile' => [],
                'controller' => 'document',
                'action' => 'mCreate',
                'body' => ['documents' => json_encode($documents)],
                'requestId' => $requestId,
                'collection' => $collection,
                'index' => $index
            ],
            'query_parameters' => []
        ];
        $mCreateResponse = [
            '_source' => [
                'hits' => $documents,
                'total' => count($documents)
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

        $result = $kuzzle->document->mCreate($index, $collection, $documents, ['requestId' => $requestId]);

        $this->assertEquals($httpResponse['result'], $result);
    }

    function testMCreateWithNoIndexName()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->document->mCreate('', 'collection', ['content' => 'someContent'], []);

            $this->fail('KuzzleTest::testMCreateWithNoIndexName => Should raise an exception (could not be called without index nor collection)');
        } catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Document::mCreate: index and collection parameters missing or documents parameter format is invalid (should be an array of documents)', $e->getMessage());
        }
    }

    function testMCreateWithNoCollectionName()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->document->mCreate('index', '', ['content' => 'someContent'], []);

            $this->fail('KuzzleTest::testMCreateWithNoCollectionName => Should raise an exception (could not be called without index nor collection)');
        } catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Document::mCreate: index and collection parameters missing or documents parameter format is invalid (should be an array of documents)', $e->getMessage());
        }
    }

    function testMCreateWithNoDocuments()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->document->mCreate('index', 'collection', [], []);

            $this->fail('KuzzleTest::testMCreateWithNoDocuments => Should raise an exception (could not be called without index nor collection)');
        } catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Document::mCreate: index and collection parameters missing or documents parameter format is invalid (should be an array of documents)', $e->getMessage());
        }
    }

    function testMCreateOrReplaceDocument()
    {
        $url = self::FAKE_KUZZLE_HOST;
        $requestId = uniqid();
        $index = 'index';
        $collection = 'collection';

        $documents = [
            ['_id' => 'foo1', 'foo' => 'bar'],
            ['_id' => 'foo2', 'foo' => 'bar'],
        ];

        $httpRequest = [
            'route' => '/' . $index . '/' . $collection . '/_mCreateOrReplace',
            'method' => 'PUT',
            'request' => [
                'volatile' => [],
                'controller' => 'document',
                'action' => 'mCreateOrReplace',
                'body' => ['documents' => json_encode($documents)],
                'requestId' => $requestId,
                'collection' => $collection,
                'index' => $index
            ],
            'query_parameters' => []
        ];
        $mCreateOrReplaceResponse = [
            '_source' => [
                'hits' => $documents,
                'total' => count($documents)
            ]
        ];
        $httpResponse = [
            'error' => null,
            'result' => $mCreateOrReplaceResponse
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

        $result = $kuzzle->document->mCreateOrReplace($index, $collection, $documents, ['requestId' => $requestId]);

        $this->assertEquals($httpResponse['result'], $result);
    }

    function testMCreateOrReplaceWithNoIndexName()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->document->mCreateOrReplace('', 'collection', ['content' => 'someContent'], []);

            $this->fail('KuzzleTest::testMCreateOrReplaceWithNoIndexName => Should raise an exception (could not be called without index nor collection)');
        } catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Document::mCreateOrReplace: index and collection parameters missing or documents parameter format is invalid (should be an array of documents)', $e->getMessage());
        }
    }

    function testMCreateOrReplaceWithNoCollectionName()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->document->mCreateOrReplace('index', '', ['content' => 'someContent'], []);

            $this->fail('KuzzleTest::testMCreateOrReplaceWithNoCollectionName => Should raise an exception (could not be called without index nor collection)');
        } catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Document::mCreateOrReplace: index and collection parameters missing or documents parameter format is invalid (should be an array of documents)', $e->getMessage());
        }
    }

    function testMCreateOrReplaceWithNoDocuments()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->document->mCreateOrReplace('index', 'collection', [], []);

            $this->fail('KuzzleTest::testMCreateOrReplaceWithNoDocuments => Should raise an exception (could not be called without index nor collection)');
        } catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Document::mCreateOrReplace: index and collection parameters missing or documents parameter format is invalid (should be an array of documents)', $e->getMessage());
        }
    }

    function testMDeleteDocument()
    {
        $url = self::FAKE_KUZZLE_HOST;
        $requestId = uniqid();
        $index = 'index';
        $collection = 'collection';

        $documentIds = ['foo1', 'foo2'];

        $httpRequest = [
            'route' => '/' . $index . '/' . $collection . '/_mDelete',
            'method' => 'DELETE',
            'request' => [
                'volatile' => [],
                'controller' => 'document',
                'action' => 'mDelete',
                'body' => ['ids' => json_encode($documentIds)],
                'requestId' => $requestId,
                'collection' => $collection,
                'index' => $index
            ],
            'query_parameters' => []
        ];
        $mDeleteResponse = [
            '_source' => [
                'hits' => $documentIds,
                'total' => count($documentIds)
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

        $result = $kuzzle->document->mDelete($index, $collection, $documentIds, ['requestId' => $requestId]);

        $this->assertEquals($httpResponse['result'], $result);
    }

    function testMDeleteWithNoIndexName()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->document->mDelete('', 'collection', ['content' => 'someContent'], []);

            $this->fail('KuzzleTest::testMDeleteWithNoIndexName => Should raise an exception (could not be called without index nor collection)');
        } catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Document::mDelete: index and collection parameters missing or documents parameter format is invalid (should be an array of document IDs)', $e->getMessage());
        }
    }

    function testMDeleteWithNoCollectionName()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->document->mDelete('index', '', ['content' => 'someContent'], []);

            $this->fail('KuzzleTest::testMDeleteWithNoCollectionName => Should raise an exception (could not be called without index nor collection)');
        } catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Document::mDelete: index and collection parameters missing or documents parameter format is invalid (should be an array of document IDs)', $e->getMessage());
        }
    }

    function testMDeleteWithNoDocumentIds()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->document->mDelete('index', 'collection', [], []);

            $this->fail('KuzzleTest::testMDeleteWithNoDocumentIds => Should raise an exception (could not be called without index nor collection)');
        } catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Document::mDelete: index and collection parameters missing or documents parameter format is invalid (should be an array of document IDs)', $e->getMessage());
        }
    }

    function testMGetDocument()
    {
        $url = self::FAKE_KUZZLE_HOST;
        $requestId = uniqid();
        $index = 'index';
        $collection = 'collection';

        $documentIds = ['foo1', 'foo2'];

        $httpRequest = [
            'route' => '/' . $index . '/' . $collection . '/_mGet',
            'method' => 'POST',
            'request' => [
                'volatile' => [],
                'controller' => 'document',
                'action' => 'mGet',
                'body' => ['ids' => json_encode($documentIds)],
                'requestId' => $requestId,
                'collection' => $collection,
                'index' => $index
            ],
            'query_parameters' => []
        ];
        $mGetResponse = [
            '_source' => [
                'hits' => $documentIds,
                'total' => count($documentIds)
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

        $result = $kuzzle->document->mGet($index, $collection, $documentIds, ['requestId' => $requestId]);

        $this->assertEquals($httpResponse['result'], $result);
    }

    function testMGetWithNoIndexName()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->document->mGet('', 'collection', ['content' => 'someContent'], []);

            $this->fail('KuzzleTest::testMGetWithNoIndexName => Should raise an exception (could not be called without index nor collection)');
        } catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Document::mGet: index and collection parameters missing or documents parameter format is invalid (should be an array of document IDs)', $e->getMessage());
        }
    }

    function testMGetWithNoCollectionName()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->document->mGet('index', '', ['content' => 'someContent'], []);

            $this->fail('KuzzleTest::testMGetWithNoCollectionName => Should raise an exception (could not be called without index nor collection)');
        } catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Document::mGet: index and collection parameters missing or documents parameter format is invalid (should be an array of document IDs)', $e->getMessage());
        }
    }

    function testMGetWithNoDocumentIds()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->document->mGet('index', 'collection', [], []);

            $this->fail('KuzzleTest::testMGetWithNoDocumentIds => Should raise an exception (could not be called without index nor collection)');
        } catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Document::mGet: index and collection parameters missing or documents parameter format is invalid (should be an array of document IDs)', $e->getMessage());
        }
    }

    function testMReplace()
    {
        $url = self::FAKE_KUZZLE_HOST;
        $requestId = uniqid();
        $index = 'index';
        $collection = 'collection';

        $documents = [
            ['_id' => 'foo1', 'foo' => 'bar'],
            ['_id' => 'foo2', 'foo' => 'bar'],
        ];

        $httpRequest = [
            'route' => '/' . $index . '/' . $collection . '/_mReplace',
            'method' => 'PUT',
            'request' => [
                'volatile' => [],
                'controller' => 'document',
                'action' => 'mReplace',
                'body' => ['documents' => json_encode($documents)],
                'requestId' => $requestId,
                'collection' => $collection,
                'index' => $index
            ],
            'query_parameters' => []
        ];
        $mReplaceResponse = [
            '_source' => [
                'hits' => $documents,
                'total' => count($documents)
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

        $result = $kuzzle->document->mReplace($index, $collection, $documents, ['requestId' => $requestId]);

        $this->assertEquals($httpResponse['result'], $result);
    }

    function testMReplaceWithNoIndexName()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->document->mReplace('', 'collection', ['content' => 'someContent'], []);

            $this->fail('KuzzleTest::testMReplaceWithNoIndexName => Should raise an exception (could not be called without index nor collection)');
        } catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Document::mReplace: index and collection parameters missing or documents parameter format is invalid (should be an array of documents)', $e->getMessage());
        }
    }

    function testMReplaceWithNoCollectionName()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->document->mReplace('index', '', ['content' => 'someContent'], []);

            $this->fail('KuzzleTest::testMReplaceWithNoCollectionName => Should raise an exception (could not be called without index nor collection)');
        } catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Document::mReplace: index and collection parameters missing or documents parameter format is invalid (should be an array of documents)', $e->getMessage());
        }
    }

    function testMReplaceWithNoDocuments()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->document->mReplace('index', 'collection', [], []);

            $this->fail('KuzzleTest::testMReplaceWithNoDocuments => Should raise an exception (could not be called without index nor collection)');
        } catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Document::mReplace: index and collection parameters missing or documents parameter format is invalid (should be an array of documents)', $e->getMessage());
        }
    }

    function testMUpdate()
    {
        $url = self::FAKE_KUZZLE_HOST;
        $requestId = uniqid();
        $index = 'index';
        $collection = 'collection';

        $documents = [
            ['_id' => 'foo1', 'foo' => 'bar'],
            ['_id' => 'foo2', 'foo' => 'bar'],
        ];

        $httpRequest = [
            'route' => '/' . $index . '/' . $collection . '/_mUpdate',
            'method' => 'PUT',
            'request' => [
                'volatile' => [],
                'controller' => 'document',
                'action' => 'mUpdate',
                'body' => ['documents' => json_encode($documents)],
                'requestId' => $requestId,
                'collection' => $collection,
                'index' => $index
            ],
            'query_parameters' => []
        ];
        $mUpdateResponse = [
            '_source' => [
                'hits' => $documents,
                'total' => count($documents)
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

        $result = $kuzzle->document->mUpdate($index, $collection, $documents, ['requestId' => $requestId]);

        $this->assertEquals($httpResponse['result'], $result);
    }

    function testMUpdateWithNoIndexName()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->document->mUpdate('', 'collection', ['content' => 'someContent'], []);

            $this->fail('KuzzleTest::testMUpdateWithNoIndexName => Should raise an exception (could not be called without index nor collection)');
        } catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Document::mUpdate: index and collection parameters missing or documents parameter format is invalid (should be an array of documents)', $e->getMessage());
        }
    }

    function testMUpdateWithNoCollectionName()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->document->mUpdate('index', '', ['content' => 'someContent'], []);

            $this->fail('KuzzleTest::testMUpdateWithNoCollectionName => Should raise an exception (could not be called without index nor collection)');
        } catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Document::mUpdate: index and collection parameters missing or documents parameter format is invalid (should be an array of documents)', $e->getMessage());
        }
    }

    function testMUpdateWithNoDocuments()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->document->mUpdate('index', 'collection', [], []);

            $this->fail('KuzzleTest::testMUpdateWithNoDocuments => Should raise an exception (could not be called without index nor collection)');
        } catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Document::mUpdate: index and collection parameters missing or documents parameter format is invalid (should be an array of documents)', $e->getMessage());
        }
    }
}
