<?php

use Kuzzle\Kuzzle;

class MemoryStorageTest extends \PHPUnit_Framework_TestCase
{
    public function testCallUndefinedCommand()
    {
        // Arrange
        $url = KuzzleTest::FAKE_KUZZLE_URL;

        try {
            $kuzzle = new Kuzzle($url);
            $memoryStorage = $kuzzle->memoryStorage();

            $memoryStorage->undefined();
            $this->fail('MemoryStorageTest::testCallUndefinedCommand => Should raise an exception');
        }
        catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('MemoryStorage: Command "undefined" not found', $e->getMessage());
        }
    }

    public function testCommand()
    {
        // Arrange
        $url = KuzzleTest::FAKE_KUZZLE_URL;
        $id = uniqid();
        $start = 10;
        $stop = 15;
        $opts = ['withscores' => true];

        try {
            $kuzzle = $this
                ->getMockBuilder('\Kuzzle\Kuzzle')
                ->setMethods(['emitRestRequest'])
                ->setConstructorArgs([$url])
                ->getMock();

            $options = [
                'requestId' => uniqid()
            ];

            $httpRequest = [
                'route' => '/api/1.0/ms/_zrevrange/' . $id . '/' . $start . '/' . $stop,
                'request' => [
                    'action' => 'zrevrange',
                    'controller' => 'memoryStorage',
                    'metadata' => [],
                    'requestId' => $options['requestId'],
                    '_id' => $id,
                    'body' => [
                        'start' => $start,
                        'stop' => $stop,
                        'withscores' => $opts['withscores']
                    ]
                ],
                'method' => 'GET'
            ];

            $httpResponse = [];

            $kuzzle
                ->expects($this->once())
                ->method('emitRestRequest')
                ->with($httpRequest)
                ->willReturn($httpResponse);

            /**
             * @var Kuzzle $kuzzle
             */
            $memoryStorage = $kuzzle->memoryStorage();

            $memoryStorage->zrevrange($id, $start, $stop, $opts, $options);
        }
        catch (Exception $e) {
            $this->fail('MemoryStorageTest::testCommand => Should not raise an exception');
        }
    }
}