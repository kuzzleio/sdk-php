<?php

use Kuzzle\Util\CurlRequest;
use Kuzzle\Bulk;

class BulkTest extends \PHPUnit_Framework_TestCase
{
    const FAKE_KUZZLE_HOST = '127.0.0.1';
    const FAKE_KUZZLE_URL = 'http://127.0.0.1:7512';

    public function testImport()
    {
        $url = self::FAKE_KUZZLE_HOST;
        $data = [];

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
            'route' => '/_bulk',
            'request' => [
                'action' => 'import',
                'controller' => 'bulk',
                'body' => [
                    'bulkData' => json_encode($data)
                ],
                'volatile' => [],
                'requestId' => $options['requestId']
            ],
            'method' => 'POST',
            'query_parameters' => []
        ];

        // mock response
        $importResponse = ["hits" => []];
        $httpResponse = [
            'error' => null,
            'result' => $importResponse
        ];

        $kuzzle
            ->expects($this->once())
            ->method('emitRestRequest')
            ->with($httpRequest)
            ->willReturn($httpResponse);

        /**
         * @var \Kuzzle\Kuzzle $kuzzle
         */
        $response = $kuzzle->bulk->import($data, $options);

        $this->assertEquals($importResponse, $response);
    }
}
