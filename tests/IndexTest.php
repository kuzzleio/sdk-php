<?php

use Kuzzle\Util\CurlRequest;

class IndexTest extends \PHPUnit_Framework_TestCase
{
    const FAKE_KUZZLE_HOST = '127.0.0.1';
    const FAKE_KUZZLE_URL = 'http://127.0.0.1:7512';

    public function testCreate()
    {
        $url = self::FAKE_KUZZLE_HOST;
        $index = 'foo';

        $kuzzle = $this
            ->getMockBuilder('\Kuzzle\Kuzzle')
            ->setMethods(['emitRestRequest'])
            ->setConstructorArgs([$url])
            ->getMock();

        $options = [
            'requestId' => uniqid()
        ];

        // mock http request
        $httpRequest = [
            'route' => '/' . $index . '/_create',
            'request' => [
                'action' => 'create',
                'controller' => 'index',
                'volatile' => [],
                'index' => $index,
                'body' => [
                    'index' => $index
                ],
                'requestId' => $options['requestId']
            ],
            'method' => 'POST',
            'query_parameters' => []
        ];

        // mock response
        $createIndexResponse = ['acknowledged' => true];
        $httpResponse = [
            'error' => null,
            'result' => $createIndexResponse
        ];

        $kuzzle
            ->expects($this->once())
            ->method('emitRestRequest')
            ->with($httpRequest)
            ->willReturn($httpResponse);

        /**
         * @var \Kuzzle\Kuzzle $kuzzle
         */
        $response = $kuzzle->index->create($index, $options);

        $this->assertEquals($createIndexResponse, $response);
    }

    public function testDelete()
    {
        $url = self::FAKE_KUZZLE_HOST;
        $index = 'foo';

        $kuzzle = $this
            ->getMockBuilder('\Kuzzle\Kuzzle')
            ->setMethods(['emitRestRequest'])
            ->setConstructorArgs([$url])
            ->getMock();

        $options = [
            'requestId' => uniqid()
        ];

        // mock http request
        $httpRequest = [
            'route' => '/' . $index,
            'request' => [
                'action' => 'delete',
                'controller' => 'index',
                'index' => $index,
                'volatile' => [],
                'requestId' => $options['requestId']
            ],
            'method' => 'DELETE',
            'query_parameters' => []
        ];

        // mock response
        $deleteIndexResponse = ['acknowledged' => true];
        $httpResponse = [
            'error' => null,
            'result' => $deleteIndexResponse
        ];

        $kuzzle
            ->expects($this->once())
            ->method('emitRestRequest')
            ->with($httpRequest)
            ->willReturn($httpResponse);

        /**
         * @var \Kuzzle\Kuzzle $kuzzle
         */
        $response = $kuzzle->index->deleteIndex($index, $options);

        $this->assertEquals($deleteIndexResponse, $response);
    }

    public function testExists()
    {
        $url = self::FAKE_KUZZLE_HOST;
        $index = 'foo';

        $kuzzle = $this
            ->getMockBuilder('\Kuzzle\Kuzzle')
            ->setMethods(['emitRestRequest'])
            ->setConstructorArgs([$url])
            ->getMock();

        $options = [
            'requestId' => uniqid()
        ];

        // mock http request
        $httpRequest = [
            'route' => '/' . $index . '/_exists',
            'request' => [
                'action' => 'exists',
                'controller' => 'index',
                'index' => $index,
                'volatile' => [],
                'requestId' => $options['requestId']
            ],
            'method' => 'GET',
            'query_parameters' => []
        ];

        // mock response
        $existsResponse = true;
        $httpResponse = [
            'error' => null,
            'result' => $existsResponse
        ];

        $kuzzle
            ->expects($this->once())
            ->method('emitRestRequest')
            ->with($httpRequest)
            ->willReturn($httpResponse);

        /**
         * @var \Kuzzle\Kuzzle $kuzzle
         */
        $response = $kuzzle->index->exists($index, $options);

        $this->assertEquals($existsResponse, $response);
    }


    public function testList()
    {
        $url = self::FAKE_KUZZLE_HOST;

        $kuzzle = $this
            ->getMockBuilder('\Kuzzle\Kuzzle')
            ->setMethods(['emitRestRequest'])
            ->setConstructorArgs([$url])
            ->getMock();

        $options = [
            'requestId' => uniqid()
        ];

        // mock http request
        $httpRequest = [
            'route' => '/_list',
            'request' => [
                'action' => 'list',
                'controller' => 'index',
                'volatile' => [],
                'requestId' => $options['requestId']
            ],
            'method' => 'GET',
            'query_parameters' => []
        ];

        // mock response
        $listIndexesResponse = ['indexes' => []];
        $httpResponse = [
            'error' => null,
            'result' => $listIndexesResponse
        ];

        $kuzzle
            ->expects($this->once())
            ->method('emitRestRequest')
            ->with($httpRequest)
            ->willReturn($httpResponse);

        /**
         * @var \Kuzzle\Kuzzle $kuzzle
         */
        $response = $kuzzle->index->listIndexes($options);

        $this->assertEquals($listIndexesResponse['indexes'], $response);
    }

    public function testRefresh()
    {
        $url = self::FAKE_KUZZLE_HOST;
        $index = 'index';

        $kuzzle = $this
            ->getMockBuilder('\Kuzzle\Kuzzle')
            ->setMethods(['emitRestRequest'])
            ->setConstructorArgs([$url])
            ->getMock();

        $options = [
            'requestId' => uniqid()
        ];

        // mock http request
        $httpRequest = [
            'route' => '/' . $index . '/_refresh',
            'request' => [
                'index' => $index,
                'action' => 'refresh',
                'controller' => 'index',
                'index' => $index,
                'volatile' => [],
                'requestId' => $options['requestId']
            ],
            'method' => 'POST',
            'query_parameters' => []
        ];

        // mock response
        $refreshIndexResponse = [];
        $httpResponse = [
            'error' => null,
            'result' => $refreshIndexResponse
        ];

        $kuzzle
            ->expects($this->once())
            ->method('emitRestRequest')
            ->with($httpRequest)
            ->willReturn($httpResponse);

        /**
         * @var \Kuzzle\Kuzzle $kuzzle
         */
        $response = $kuzzle->index->refresh($index, $options);

        $this->assertEquals($refreshIndexResponse, $response);
    }

    public function testRefreshWithoutIndex()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->index->refresh('', []);

            $this->fail('KuzzleTest::testRefreshWithoutIndex => Should raise an exception (could not be called without index nor default index)');
        }
        catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Index::refresh: Unable to refresh index: no index specified', $e->getMessage());
        }
    }

    public function testGetAutoRefresh()
    {
        $url = self::FAKE_KUZZLE_HOST;
        $index = 'foo';

        $kuzzle = $this
            ->getMockBuilder('\Kuzzle\Kuzzle')
            ->setMethods(['emitRestRequest'])
            ->setConstructorArgs([$url])
            ->getMock();

        $options = [
            'requestId' => uniqid()
        ];

        // mock http request
        $httpRequest = [
            'route' => '/' . $index . '/_autoRefresh',
            'request' => [
                'action' => 'getAutoRefresh',
                'controller' => 'index',
                'index' => $index,
                'index' => 'foo',
                'volatile' => [],
                'requestId' => $options['requestId']
            ],
            'method' => 'GET',
            'query_parameters' => []
        ];

        // mock response
        $getAutoRefreshResponse = true;
        $httpResponse = [
            'error' => null,
            'result' => $getAutoRefreshResponse
        ];

        $kuzzle
            ->expects($this->once())
            ->method('emitRestRequest')
            ->with($httpRequest)
            ->willReturn($httpResponse);

        /**
         * @var \Kuzzle\Kuzzle $kuzzle
         */
        $response = $kuzzle->index->getAutoRefresh($index, $options);

        $this->assertEquals($getAutoRefreshResponse, $response);
    }

    public function testGetAutoRefreshWithoutIndex()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->index->getAutoRefresh('', []);

            $this->fail('KuzzleTest::testGetAutoRefreshWithoutIndex => Should raise an exception (could not be called without index nor default index)');
        }
        catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Index::getAutoRefresh: Unable to get auto refresh on index: no index specified', $e->getMessage());
        }
    }

    public function testSetAutoRefresh()
    {
        $url = self::FAKE_KUZZLE_HOST;
        $index = 'index';
        $autoRefresh = true;

        $kuzzle = $this
            ->getMockBuilder('\Kuzzle\Kuzzle')
            ->setMethods(['emitRestRequest'])
            ->setConstructorArgs([$url])
            ->getMock();

        $options = [
            'requestId' => uniqid()
        ];

        // mock http request
        $httpRequest = [
            'route' => '/' . $index . '/_autoRefresh',
            'request' => [
                'index' => $index,
                'action' => 'setAutoRefresh',
                'controller' => 'index',
                'index' => $index,
                'volatile' => [],
                'body' => [
                    'autoRefresh' => $autoRefresh
                ],
                'requestId' => $options['requestId']
            ],
            'method' => 'POST',
            'query_parameters' => []
        ];

        // mock response
        $setAutoRefreshResponse = [];
        $httpResponse = [
            'error' => null,
            'result' => $setAutoRefreshResponse
        ];

        $kuzzle
            ->expects($this->once())
            ->method('emitRestRequest')
            ->with($httpRequest)
            ->willReturn($httpResponse);

        /**
         * @var \Kuzzle\Kuzzle $kuzzle
         */
        $response = $kuzzle->index->setAutoRefresh($index, $autoRefresh, $options);

        $this->assertEquals($setAutoRefreshResponse, $response);
    }

    public function testSetAutoRefreshWithoutIndex()
    {
        // Arrange
        $url = self::FAKE_KUZZLE_HOST;

        try {
            $kuzzle = new \Kuzzle\Kuzzle($url);
            $kuzzle->index->setAutoRefresh('', false, []);

            $this->fail('KuzzleTest::testSetAutoRefreshWithoutIndex => Should raise an exception (could not be called without index nor default index)');
        }
        catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('Kuzzle\Index::setAutoRefresh: Unable to set auto refresh on index: no index specified or invalid value of autoRefresh', $e->getMessage());
        }
    }

    public function testRefreshInternal()
    {
        $url = self::FAKE_KUZZLE_HOST;

        $kuzzle = $this
            ->getMockBuilder('\Kuzzle\Kuzzle')
            ->setMethods(['emitRestRequest'])
            ->setConstructorArgs([$url])
            ->getMock();

        $options = [
            'requestId' => uniqid()
        ];

        // mock http request
        $httpRequest = [
            'route' => '/_refreshInternal',
            'request' => [
                'action' => 'refreshInternal',
                'controller' => 'index',
                'volatile' => [],
                'requestId' => $options['requestId']
            ],
            'method' => 'POST',
            'query_parameters' => []
        ];

        // mock response
        $refreshInternalResponse = ["acknowledged" => true];
        $httpResponse = [
            'error' => null,
            'result' => $refreshInternalResponse
        ];

        $kuzzle
            ->expects($this->once())
            ->method('emitRestRequest')
            ->with($httpRequest)
            ->willReturn($httpResponse);

        /**
         * @var \Kuzzle\Kuzzle $kuzzle
         */
        $response = $kuzzle->index->refreshInternal($options);

        $this->assertEquals($refreshInternalResponse, $response);
    }

    public function testMDelete()
    {
        $url = self::FAKE_KUZZLE_HOST;
        $indexes = [
            'foo',
            'bar'
        ];

        $kuzzle = $this
            ->getMockBuilder('\Kuzzle\Kuzzle')
            ->setMethods(['emitRestRequest'])
            ->setConstructorArgs([$url])
            ->getMock();

        $options = [
            'requestId' => uniqid()
        ];

        // mock http request
        $httpRequest = [
            'route' => '/_mDelete',
            'request' => [
                'action' => 'mDelete',
                'controller' => 'index',
                'volatile' => [],
                'body' => [
                  'indexes' => json_encode($indexes)
                ],
                'requestId' => $options['requestId']
            ],
            'method' => 'DELETE',
            'query_parameters' => []
        ];

        // mock response
        $mDeleteResponse = [
            'deleted' => [
                'foo',
                'bar'
            ]
        ];
        $httpResponse = [
            'error' => null,
            'result' => $mDeleteResponse
        ];

        $kuzzle
            ->expects($this->once())
            ->method('emitRestRequest')
            ->with($httpRequest)
            ->willReturn($httpResponse);

        /**
         * @var \Kuzzle\Kuzzle $kuzzle
         */
        $response = $kuzzle->index->mDelete($indexes, $options);

        $this->assertEquals($mDeleteResponse, $response);
    }
}
