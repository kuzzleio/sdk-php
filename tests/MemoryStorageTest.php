<?php

use Kuzzle\Kuzzle;

class MemoryStorageTest extends \PHPUnit_Framework_TestCase
{
    public function testCallUndefinedCommand()
    {
        $kuzzle = new Kuzzle(KuzzleTest::FAKE_KUZZLE_HOST);
        $memoryStorage = $kuzzle->memoryStorage();

        try {
            $memoryStorage->undefined();
            $this->fail('MemoryStorageTest::testCallUndefinedCommand => Should raise an exception');
        }
        catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('MemoryStorage: Command "undefined" not found', $e->getMessage());
        }
    }

    public function testWrongNumberOfArguments()
    {
        $kuzzle = new Kuzzle(KuzzleTest::FAKE_KUZZLE_HOST);
        $memoryStorage = $kuzzle->memoryStorage();

        try {
            $memoryStorage->dbsize('foo', []);
            $this->fail('MemoryStorageTest::testWrongNumberOfArguments(dbsize) => Should raise an exception');
        }
        catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('MemoryStorage.dbsize: Too many parameters provided', $e->getMessage());
        }

        try {
            $memoryStorage->mget();
            $this->fail('MemoryStorageTest::testWrongNumberOfArguments(mget) => Should raise an exception');
        }
        catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('MemoryStorage.mget: Missing parameter "keys"', $e->getMessage());
        }

        try {
            $memoryStorage->ping(123);
            $this->fail('MemoryStorageTest::testWrongNumberOfArguments(ping) => Should raise an exception');
        }
        catch (Exception $e) {
            $this->assertInstanceOf('InvalidArgumentException', $e);
            $this->assertEquals('MemoryStorage.ping: Invalid optional parameter (expected an associative array)', $e->getMessage());
        }
    }

    public function testCommand()
    {
        // Arrange
        $url = KuzzleTest::FAKE_KUZZLE_HOST;
        $id = uniqid();
        $start = 10;
        $stop = 15;

        $kuzzle = $this
            ->getMockBuilder('\Kuzzle\Kuzzle')
            ->setMethods(['emitRestRequest'])
            ->setConstructorArgs([$url])
            ->getMock();

        $options = [
            'requestId' => uniqid()
        ];

        $httpRequest = [
            'route' => '/ms/_zrevrange/' . $id,
            'request' => [
                'action' => 'zrevrange',
                'controller' => 'memoryStorage',
                'metadata' => [],
                'requestId' => $options['requestId'],
                '_id' => $id,
                'start' => $start,
                'stop' => $stop,
                'options' => ['withscores']
            ],
            'method' => 'GET',
            'query_parameters' => []
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

        $memoryStorage->zrevrange($id, $start, $stop, $options);
    }
}
