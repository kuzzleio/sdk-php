<?php

use Kuzzle\Util\CurlRequest;

class ServerTest extends \PHPUnit_Framework_TestCase
{
    const FAKE_KUZZLE_HOST = '127.0.0.1';
    const FAKE_KUZZLE_URL = 'http://127.0.0.1:7512';

    public function testNow()
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
            'route' => '/_now',
            'request' => [
                'action' => 'now',
                'controller' => 'server',
                'volatile' => [],
                'requestId' => $options['requestId']
            ],
            'method' => 'GET',
            'query_parameters' => []
        ];

        // mock response (timestamp is given in milliseconds)
        $logoutResponse = ['now' => (time()) * 1000];
        $httpResponse = [
            'error' => null,
            'result' => $logoutResponse
        ];

        $kuzzle
            ->expects($this->once())
            ->method('emitRestRequest')
            ->with($httpRequest)
            ->willReturn($httpResponse);

        /**
         * @var \Kuzzle\Kuzzle $kuzzle
         */
        $response = $kuzzle->server->now($options);

        $this->assertInstanceOf('DateTime', $response);
        $this->assertEquals($logoutResponse['now'] / 1000, $response->getTimestamp());
    }

    public function testGetServerInfo()
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
            'route' => '/_serverInfo',
            'request' => [
                'action' => 'info',
                'controller' => 'server',
                'volatile' => [],
                'requestId' => $options['requestId']
            ],
            'method' => 'GET',
            'query_parameters' => []
        ];

        // mock response
        $getServerInfoResponse = ['serverInfo' => []];
        $httpResponse = [
            'error' => null,
            'result' => $getServerInfoResponse
        ];

        $kuzzle
            ->expects($this->once())
            ->method('emitRestRequest')
            ->with($httpRequest)
            ->willReturn($httpResponse);

        $response = $kuzzle->server->getInfo($options);

        $this->assertEquals($getServerInfoResponse['serverInfo'], $response);
    }

    public function testGetConfig()
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
            'route' => '/_getConfig',
            'request' => [
                'action' => 'getConfig',
                'controller' => 'server',
                'volatile' => [],
                'requestId' => $options['requestId']
            ],
            'method' => 'GET',
            'query_parameters' => []
        ];

        // mock response
        $getConfigResponse = [];
        $httpResponse = [
            'error' => null,
            'result' => $getConfigResponse
        ];

        $kuzzle
            ->expects($this->once())
            ->method('emitRestRequest')
            ->with($httpRequest)
            ->willReturn($httpResponse);

        /**
         * @var \Kuzzle\Kuzzle $kuzzle
         */
        $response = $kuzzle->server->getConfig($options);

        $this->assertEquals([$getConfigResponse], $response);
    }

    public function testGetStatistics()
    {
        $url = self::FAKE_KUZZLE_HOST;
        $statsTime = time();

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
            'route' => '/_getStats',
            'request' => [
                'action' => 'getStats',
                'controller' => 'server',
                'volatile' => [],
                'body' => [
                    'startTime' => $statsTime
                ],
                'requestId' => $options['requestId']
            ],
            'method' => 'POST',
            'query_parameters' => []
        ];

        // mock response
        $getStatsResponse = ['hits' => []];
        $httpResponse = [
            'error' => null,
            'result' => $getStatsResponse
        ];

        $kuzzle
            ->expects($this->once())
            ->method('emitRestRequest')
            ->with($httpRequest)
            ->willReturn($httpResponse);

        /**
         * @var \Kuzzle\Kuzzle $kuzzle
         */
        $response = $kuzzle->server->getStats($statsTime, $options);

        $this->assertEquals($getStatsResponse['hits'], $response);
    }

    public function testGetLastStatistics()
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
            'route' => '/_getLastStats',
            'request' => [
                'action' => 'getLastStats',
                'controller' => 'server',
                'volatile' => [],
                'requestId' => $options['requestId']
            ],
            'method' => 'GET',
            'query_parameters' => []
        ];

        // mock response
        $getLastStatsResponse = [];
        $httpResponse = [
            'error' => null,
            'result' => $getLastStatsResponse
        ];

        $kuzzle
            ->expects($this->once())
            ->method('emitRestRequest')
            ->with($httpRequest)
            ->willReturn($httpResponse);

        /**
         * @var \Kuzzle\Kuzzle $kuzzle
         */
        $response = $kuzzle->server->getLastStats($options);

        $this->assertEquals([$getLastStatsResponse], $response);
    }

    public function testGetAllStatistics()
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
            'route' => '/_getAllStats',
            'request' => [
                'action' => 'getAllStats',
                'controller' => 'server',
                'volatile' => [],
                'requestId' => $options['requestId']
            ],
            'method' => 'GET',
            'query_parameters' => []
        ];

        // mock response
        $getAllStatisticsResponse = ['hits' => []];
        $httpResponse = [
            'error' => null,
            'result' => $getAllStatisticsResponse
        ];

        $kuzzle
            ->expects($this->once())
            ->method('emitRestRequest')
            ->with($httpRequest)
            ->willReturn($httpResponse);

        /**
         * @var \Kuzzle\Kuzzle $kuzzle
         */
        $response = $kuzzle->server->getAllStats($options);

        $this->assertEquals($getAllStatisticsResponse['hits'], $response);
    }

    public function testAdminExists()
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
            'route' => '/_adminExists',
            'request' => [
                'action' => 'adminExists',
                'controller' => 'server',
                'volatile' => [],
                'requestId' => $options['requestId']
            ],
            'method' => 'GET',
            'query_parameters' => []
        ];

        // mock response
        $adminExistsResponse = null;
        $httpResponse = [
            'error' => null,
            'result' => $adminExistsResponse
        ];

        $kuzzle
            ->expects($this->once())
            ->method('emitRestRequest')
            ->with($httpRequest)
            ->willReturn($httpResponse);

        /**
         * @var \Kuzzle\Kuzzle $kuzzle
         */
        $response = $kuzzle->server->adminExists($options);

        $this->assertEquals($adminExistsResponse, $response);
    }
}
