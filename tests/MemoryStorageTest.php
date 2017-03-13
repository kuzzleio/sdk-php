<?php

use Kuzzle\Kuzzle;
use Kuzzle\MemoryStorage;
use PHPUnit\Framework\TestCase;


class MemoryStorageTest extends TestCase
{
    protected $url = KuzzleTest::FAKE_KUZZLE_HOST;
    /**
     * @var Kuzzle $kuzzle
     */
    protected $kuzzle = null;
    protected $memoryStorage;
    protected $options;

    protected function SetUp() {
        $this->options = ['requestId' => uniqid()];

        $this->kuzzle = $this
            ->getMockBuilder('\Kuzzle\Kuzzle')
            ->setMethods(['emitRestRequest'])
            ->setConstructorArgs([$this->url])
            ->getMock();

        $this->memoryStorage = new MemoryStorage($this->kuzzle);
    }

    public function testCallUndefinedCommand()
    {
        $kuzzle = new Kuzzle($this->url);
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

    /**
     * @param {string} $command
     * @param {string} $verb
     * @param {string} $route
     * @param {array} $request
     * @param {array} $opts
     * @param $response
     */
    private function SetupMemoryStorageCommand($command, $verb, $route, $request, $opts, $response)
    {
        $httpRequest = [
            'route' => $route,
            'request' => [
                'action' => $command,
                'controller' => 'memoryStorage',
                'metadata' => [],
                'requestId' => $this->options['requestId'],
            ],
            'method' => $verb,
            'query_parameters' => $opts
        ];

        $httpRequest['request'] = array_merge($httpRequest['request'], $request);

        $this->kuzzle
            ->expects($this->once())
            ->method('emitRestRequest')
            ->with($httpRequest)
            ->willReturn(['result' => $response]);
    }

    public function testWrongNumberOfArguments()
    {
        $kuzzle = new Kuzzle($this->url);
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

    public function testAppend() {
        $this->SetupMemoryStorageCommand('append', 'POST', '/ms/_append/key', [
           '_id' => 'key',
            'body' => ['value' => 'foo']
        ], [], 3);

        $result = $this->memoryStorage->append('key', 'foo', $this->options);
        $this->assertEquals($result, 3);
    }

    public function testBitcount() {
        $this->SetupMemoryStorageCommand(
            'bitcount',
            'GET',
            '/ms/_bitcount/key',
            ['_id' => 'key'],
            ['start' => 1, 'end' => 3],
            3
        );

        $this->options['start'] = 1;
        $this->options['end'] = 3;

        $result = $this->memoryStorage->bitcount('key', $this->options);
        $this->assertEquals($result, 3);
    }

    public function testBitop() {
        $this->SetupMemoryStorageCommand('bitop', 'POST', '/ms/_bitop/key', [
            '_id' => 'key',
            'body' => [
                'operation' => 'AND',
                'keys' => ['foo', 'bar', 'baz']
            ]
        ], [], 3);

        $result = $this->memoryStorage->bitop('key', 'AND', ['foo', 'bar', 'baz'], $this->options);
        $this->assertEquals($result, 3);
    }

    public function testBitpos() {
        $this->SetupMemoryStorageCommand(
            'bitpos',
            'GET',
            '/ms/_bitpos/key',
            ['_id' => 'key'],
            ['start' => 1, 'end' => 3, 'bit' => 0],
            3
        );

        $this->options['start'] = 1;
        $this->options['end'] = 3;

        $result = $this->memoryStorage->bitpos('key', 0, $this->options);
        $this->assertEquals($result, 3);
    }

    public function testDbsize() {
        $this->SetupMemoryStorageCommand('dbsize', 'GET', '/ms/_dbsize' , [], [], 3);

        $result = $this->memoryStorage->dbsize($this->options);
        $this->assertEquals($result, 3);
    }

    public function testDecr() {
        $this->SetupMemoryStorageCommand('decr', 'POST', '/ms/_decr/key', [
            '_id' => 'key'
        ], [], 'foobar');

        $result = $this->memoryStorage->decr('key', $this->options);
        $this->assertEquals($result, 'foobar');
    }

    public function testDecrby() {
        $this->SetupMemoryStorageCommand('decrby', 'POST', '/ms/_decrby/key', [
            '_id' => 'key',
            'body' => ['value' => 42]
        ], [], 47);

        $result = $this->memoryStorage->decrby('key', 42, $this->options);
        $this->assertEquals($result, 47);
    }

    public function testDel() {
        $this->SetupMemoryStorageCommand('del', 'DELETE', '/ms', [
            'body' => ['keys' => ['foo', 'bar', 'baz']]
        ], [], 3);

        $result = $this->memoryStorage->del(['foo', 'bar', 'baz'], $this->options);
        $this->assertEquals($result, 3);
    }

    public function testExists() {
        $this->SetupMemoryStorageCommand('exists', 'GET', '/ms/_exists', [],
        ['keys' => 'foo,bar,baz'], 3);

        $result = $this->memoryStorage->exists(['foo', 'bar', 'baz'], $this->options);
        $this->assertEquals($result, 3);
    }

    public function testExpire() {
        $this->SetupMemoryStorageCommand('expire', 'POST', '/ms/_expire/key', [
            '_id' => 'key',
            'body' => ['seconds' => 42]
        ], [], 0);

        $result = $this->memoryStorage->expire('key', 42, $this->options);
        $this->assertEquals($result, 0);
    }

    public function testExpireat() {
        $this->SetupMemoryStorageCommand('expireat', 'POST', '/ms/_expireat/key', [
            '_id' => 'key',
            'body' => ['timestamp' => 1234567890]
        ], [], 1);

        $result = $this->memoryStorage->expireat('key', 1234567890, $this->options);
        $this->assertEquals($result, 1);
    }

    public function testFlushdb() {
        $this->SetupMemoryStorageCommand('flushdb', 'POST', '/ms/_flushdb' , [], [], 1);

        $result = $this->memoryStorage->flushdb($this->options);
        $this->assertEquals($result, 1);
    }

    public function testGeoadd() {
        $points = [
            [
                'lon' => 13.361389,
                'lat' => 38.115556,
                'name' => 'Palermo'
            ],
            [
                'lon' => 15.087269,
                'lat' => 37.502669,
                'name' => 'Catania'
            ]
        ];

        $this->SetupMemoryStorageCommand('geoadd', 'POST', '/ms/_geoadd/key', [
            '_id' => 'key',
            'body' => ['points' => $points]
        ], [], 2);

        $result = $this->memoryStorage->geoadd('key', $points, $this->options);
        $this->assertEquals($result, 2);
    }

    public function testGeodist() {
        $this->SetupMemoryStorageCommand(
            'geodist',
            'GET',
            '/ms/_geodist/key/foo/bar',
            ['_id' => 'key'],
            ['unit' => 'ft'],
            '166274.1516'
        );

        $this->options['unit'] = 'ft';

        $result = $this->memoryStorage->geodist('key', 'foo' , 'bar', $this->options);
        $this->assertEquals($result, 166274.1516);
    }

    public function testGeohash() {
        $this->SetupMemoryStorageCommand(
            'geohash',
            'GET',
            '/ms/_geohash/key',
            ['_id' => 'key'],
            ['members' => 'foo,bar,baz'],
            ['abc', 'def']
        );

        $result = $this->memoryStorage->geohash('key', ['foo', 'bar', 'baz'], $this->options);
        $this->assertEquals($result, ['abc', 'def']);
    }

    public function testGeopos() {
        $this->SetupMemoryStorageCommand(
            'geopos',
            'GET',
            '/ms/_geopos/key',
            ['_id' => 'key'],
            ['members' => 'foo,bar,baz'],
            [['12.34', '56.78'], ['3.14', '2.718']]
        );

        $result = $this->memoryStorage->geopos('key', ['foo', 'bar', 'baz'], $this->options);
        $this->assertEquals($result, [[12.34, 56.78], [3.14, 2.718]]);
    }

    public function testGeoradius() {
        $this->SetupMemoryStorageCommand(
            'georadius',
            'GET',
            '/ms/_georadius/key',
            ['_id' => 'key'],
            [
                'lon' => 15,
                'lat' => 37,
                'distance' => 200,
                'unit' => 'km',
                'options' => 'count,10,asc,withcoord,withdist'
            ],
            [['Palermo', '190.4424', ['13.36', '38.11']], ['Catania', ['15.08', '37.5'], '56.44']]
        );

        $this->options['count'] = 10;
        $this->options['sort'] = 'asc';
        $this->options['withcoord'] = true;
        $this->options['withdist'] = true;

        $result = $this->memoryStorage->georadius('key', 15, 37, 200, 'km', $this->options);
        $this->assertEquals($result, [[
            'name' => 'Palermo',
            'distance' => 190.4424,
            'coordinates' => [13.36, 38.11]
        ], [
            'name' => 'Catania',
            'distance' => 56.44,
            'coordinates' => [15.08, 37.5]
        ]]);
    }

    public function testGeoradiusbymember() {
        $this->SetupMemoryStorageCommand(
            'georadiusbymember',
            'GET',
            '/ms/_georadiusbymember/key',
            ['_id' => 'key'],
            [
                'member' => 'Palermo',
                'distance' => 200,
                'unit' => 'km',
                'options' => 'count,10,asc,withcoord,withdist'
            ],
            [['Palermo', '190.4424', ['13.36', '38.11']], ['Catania', ['15.08', '37.5'], '56.44']]
        );

        $this->options['count'] = 10;
        $this->options['sort'] = 'asc';
        $this->options['withcoord'] = true;
        $this->options['withdist'] = true;

        $result = $this->memoryStorage->georadiusbymember('key', 'Palermo', 200, 'km', $this->options);
        $this->assertEquals($result, [[
            'name' => 'Palermo',
            'distance' => 190.4424,
            'coordinates' => [13.36, 38.11]
        ], [
            'name' => 'Catania',
            'distance' => 56.44,
            'coordinates' => [15.08, 37.5]
        ]]);
    }

    public function testGet() {
        $this->SetupMemoryStorageCommand(
            'get',
            'GET',
            '/ms/key',
            ['_id' => 'key'],
            [],
            'foobar'
        );

        $result = $this->memoryStorage->get('key', $this->options);
        $this->assertEquals($result, 'foobar');
    }

    public function testGetbit() {
        $this->SetupMemoryStorageCommand(
            'getbit',
            'GET',
            '/ms/_getbit/key',
            ['_id' => 'key'],
            ['offset' => 42],
            0
        );

        $result = $this->memoryStorage->getbit('key', 42, $this->options);
        $this->assertEquals($result, 0);
    }

    public function testGetrange() {
        $this->SetupMemoryStorageCommand(
            'getrange',
            'GET',
            '/ms/_getrange/key',
            ['_id' => 'key'],
            ['start' => 10, 'end' => 42],
            13
        );

        $result = $this->memoryStorage->getrange('key', 10, 42, $this->options);
        $this->assertEquals($result, 13);
    }

    public function testGetset() {
        $this->SetupMemoryStorageCommand(
            'getset',
            'POST',
            '/ms/_getset/key',
            ['_id' => 'key', 'body' => ['value' => 'foobar']],
            [],
            'barfoo'
        );

        $result = $this->memoryStorage->getset('key', 'foobar', $this->options);
        $this->assertEquals($result, 'barfoo');
    }

    public function testHdel() {
        $this->SetupMemoryStorageCommand(
            'hdel',
            'DELETE',
            '/ms/_hdel/key',
            ['_id' => 'key', 'body' => ['fields' => ['foo', 'bar', 'baz']]],
            [],
            3
        );

        $result = $this->memoryStorage->hdel('key', ['foo', 'bar', 'baz'], $this->options);
        $this->assertEquals($result, 3);
    }

    public function testHexists() {
        $this->SetupMemoryStorageCommand(
            'hexists',
            'GET',
            '/ms/_hexists/key/foobar',
            ['_id' => 'key'],
            [],
            1
        );

        $result = $this->memoryStorage->hexists('key', 'foobar', $this->options);
        $this->assertEquals($result, 1);
    }

    public function testHget() {
        $this->SetupMemoryStorageCommand(
            'hget',
            'GET',
            '/ms/_hget/key/foobar',
            ['_id' => 'key'],
            [],
            'foobar'
        );

        $result = $this->memoryStorage->hget('key', 'foobar', $this->options);
        $this->assertEquals($result, 'foobar');
    }

    public function testHgetall() {
        $this->SetupMemoryStorageCommand(
            'hgetall',
            'GET',
            '/ms/_hgetall/key',
            ['_id' => 'key'],
            [],
            ['foo' => 'bar', 'baz' => 'qux']
        );

        $result = $this->memoryStorage->hgetall('key', $this->options);
        $this->assertEquals($result, ['foo' => 'bar', 'baz' => 'qux']);
    }

    public function testHincrby() {
        $this->SetupMemoryStorageCommand(
            'hincrby',
            'POST',
            '/ms/_hincrby/key',
            ['_id' => 'key', 'body' => ['field' => 'foo', 'value' => 42]],
            [],
            50
        );

        $result = $this->memoryStorage->hincrby('key', 'foo', 42, $this->options);
        $this->assertEquals($result, 50);
    }

    public function testHincrbyfloat() {
        $this->SetupMemoryStorageCommand(
            'hincrbyfloat',
            'POST',
            '/ms/_hincrbyfloat/key',
            ['_id' => 'key', 'body' => ['field' => 'foo', 'value' => 42.5]],
            [],
            '50.123'
        );

        $result = $this->memoryStorage->hincrbyfloat('key', 'foo', 42.5, $this->options);
        $this->assertEquals($result, 50.123);
    }

    public function testHkeys() {
        $this->SetupMemoryStorageCommand(
            'hkeys',
            'GET',
            '/ms/_hkeys/key',
            ['_id' => 'key'],
            [],
            ['foo', 'bar', 'baz']
        );

        $result = $this->memoryStorage->hkeys('key', $this->options);
        $this->assertEquals($result, ['foo', 'bar', 'baz']);
    }

    public function testHlen() {
        $this->SetupMemoryStorageCommand(
            'hlen',
            'GET',
            '/ms/_hlen/key',
            ['_id' => 'key'],
            [],
            5
        );

        $result = $this->memoryStorage->hlen('key', $this->options);
        $this->assertEquals($result, 5);
    }

    public function testHmget() {
        $this->SetupMemoryStorageCommand(
            'hmget',
            'GET',
            '/ms/_hmget/key',
            ['_id' => 'key'],
            ['fields' => 'foo,bar,baz'],
            ['hey', 'I just met you', 'and this is crazy']
        );

        $result = $this->memoryStorage->hmget('key', ['foo', 'bar', 'baz'], $this->options);
        $this->assertEquals($result, ['hey', 'I just met you', 'and this is crazy']);
    }

    public function testHmset() {
        $entries = [
            ['field' => 'field1', 'value' => 'foo'],
            ['field' => 'field2', 'value' => 'bar'],
            ['field' => '...', 'value' => '...']
        ];

        $this->SetupMemoryStorageCommand(
            'hmset',
            'POST',
            '/ms/_hmset/key',
            ['_id' => 'key', 'body' => ['entries' => $entries]],
            [],
            'OK'
        );

        $result = $this->memoryStorage->hmset('key', $entries, $this->options);
        $this->assertEquals($result, 'OK');
    }

    public function testHscan() {
        $this->SetupMemoryStorageCommand(
            'hscan',
            'GET',
            '/ms/_hscan/key',
            ['_id' => 'key'],
            ['cursor' => 0],
            [18, ['foo', 'bar', 'baz', 'qux']]
        );

        $result = $this->memoryStorage->hscan('key', 0, $this->options);
        $this->assertEquals($result, [18, ['foo', 'bar', 'baz', 'qux']]);
    }

    public function testHset() {
        $this->SetupMemoryStorageCommand(
            'hset',
            'POST',
            '/ms/_hset/key',
            ['_id' => 'key', 'body' => ['field' => 'foo', 'value' => 'bar']],
            [],
            1
        );

        $result = $this->memoryStorage->hset('key', 'foo', 'bar', $this->options);
        $this->assertEquals($result, 1);
    }

    public function testHsetnx() {
        $this->SetupMemoryStorageCommand(
            'hsetnx',
            'POST',
            '/ms/_hsetnx/key',
            ['_id' => 'key', 'body' => ['field' => 'foo', 'value' => 'bar']],
            [],
            1
        );

        $result = $this->memoryStorage->hsetnx('key', 'foo', 'bar', $this->options);
        $this->assertEquals($result, 1);
    }

    public function testHstrlen() {
        $this->SetupMemoryStorageCommand(
            'hstrlen',
            'GET',
            '/ms/_hstrlen/key/foo',
            ['_id' => 'key'],
            [],
            5
        );

        $result = $this->memoryStorage->hstrlen('key', 'foo', $this->options);
        $this->assertEquals($result, 5);
    }

    public function testHvals() {
        $this->SetupMemoryStorageCommand(
            'hvals',
            'GET',
            '/ms/_hvals/key',
            ['_id' => 'key'],
            [],
            ['foo', 'bar', 'baz']
        );

        $result = $this->memoryStorage->hvals('key', $this->options);
        $this->assertEquals($result, ['foo', 'bar', 'baz']);
    }

    public function testincr() {
        $this->SetupMemoryStorageCommand(
            'incr',
            'POST',
            '/ms/_incr/key',
            ['_id' => 'key'],
            [],
            50
        );

        $result = $this->memoryStorage->incr('key', $this->options);
        $this->assertEquals($result, 50);
    }

    public function testincrby() {
        $this->SetupMemoryStorageCommand(
            'incrby',
            'POST',
            '/ms/_incrby/key',
            ['_id' => 'key', 'body' => ['value' => 42]],
            [],
            50
        );

        $result = $this->memoryStorage->incrby('key', 42, $this->options);
        $this->assertEquals($result, 50);
    }

    public function testincrbyfloat() {
        $this->SetupMemoryStorageCommand(
            'incrbyfloat',
            'POST',
            '/ms/_incrbyfloat/key',
            ['_id' => 'key', 'body' => ['value' => 42.5]],
            [],
            '50.123'
        );

        $result = $this->memoryStorage->incrbyfloat('key', 42.5, $this->options);
        $this->assertEquals($result, 50.123);
    }

    public function testKeys() {
        $this->SetupMemoryStorageCommand(
            'keys',
            'GET',
            '/ms/_keys/foo*',
            [],
            [],
            ['foo', 'foobar', 'foobarbaz']
        );

        $result = $this->memoryStorage->keys('foo*', $this->options);
        $this->assertEquals($result, ['foo', 'foobar', 'foobarbaz']);
    }

    public function testLindex() {
        $this->SetupMemoryStorageCommand(
            'lindex',
            'GET',
            '/ms/_lindex/key/3',
            ['_id' => 'key'],
            [],
            'foobar'
        );

        $result = $this->memoryStorage->lindex('key', 3, $this->options);
        $this->assertEquals($result, 'foobar');
    }

    public function testLinsert() {
        $this->SetupMemoryStorageCommand(
            'linsert',
            'POST',
            '/ms/_linsert/key',
            [
                '_id' => 'key',
                'body' => [
                    'position' => 'before',
                    'pivot' => 'foo',
                    'value' => 'bar'
                ]
            ],
            [],
            7
        );

        $result = $this->memoryStorage->linsert('key', 'before', 'foo', 'bar', $this->options);
        $this->assertEquals($result, 7);
    }

    public function testLlen() {
        $this->SetupMemoryStorageCommand(
            'llen',
            'GET',
            '/ms/_llen/key',
            ['_id' => 'key'],
            [],
            5
        );

        $result = $this->memoryStorage->llen('key', $this->options);
        $this->assertEquals($result, 5);
    }

    public function testLpop() {
        $this->SetupMemoryStorageCommand(
            'lpop',
            'POST',
            '/ms/_lpop/key',
            ['_id' => 'key'],
            [],
            'foobar'
        );

        $result = $this->memoryStorage->lpop('key', $this->options);
        $this->assertEquals($result, 'foobar');
    }

    public function testLpush() {
        $this->SetupMemoryStorageCommand(
            'lpush',
            'POST',
            '/ms/_lpush/key',
            ['_id' => 'key', 'body' => ['values' => ['foo', 'bar', 'baz']]],
            [],
            8
        );

        $result = $this->memoryStorage->lpush('key', ['foo', 'bar', 'baz'], $this->options);
        $this->assertEquals($result, 8);
    }

    public function testLpushx() {
        $this->SetupMemoryStorageCommand(
            'lpushx',
            'POST',
            '/ms/_lpushx/key',
            ['_id' => 'key', 'body' => ['value' => 'foobar']],
            [],
            8
        );

        $result = $this->memoryStorage->lpushx('key', 'foobar', $this->options);
        $this->assertEquals($result, 8);
    }

    public function testLrange() {
        $this->SetupMemoryStorageCommand(
            'lrange',
            'GET',
            '/ms/_lrange/key',
            ['_id' => 'key'],
            ['start' => 13, 'stop' => 16],
            ['foo', 'bar', 'baz']
        );

        $result = $this->memoryStorage->lrange('key', 13, 16, $this->options);
        $this->assertEquals($result, ['foo', 'bar', 'baz']);
    }

    public function testLrem() {
        $this->SetupMemoryStorageCommand(
            'lrem',
            'DELETE',
            '/ms/_lrem/key',
            ['_id' => 'key', 'body' => ['count' => 3, 'value' => 'foo']],
            [],
            3
        );

        $result = $this->memoryStorage->lrem('key', 3, 'foo', $this->options);
        $this->assertEquals($result, 3);
    }

    public function testLset() {
        $this->SetupMemoryStorageCommand(
            'lset',
            'POST',
            '/ms/_lset/key',
            ['_id' => 'key', 'body' => ['index' => 3, 'value' => 'bar']],
            [],
            'OK'
        );

        $result = $this->memoryStorage->lset('key', 3, 'bar', $this->options);
        $this->assertEquals($result, 'OK');
    }

    public function testLtrim() {
        $this->SetupMemoryStorageCommand(
            'ltrim',
            'POST',
            '/ms/_ltrim/key',
            ['_id' => 'key', 'body' => ['start' => 13, 'stop' => 42]],
            [],
            'OK'
        );

        $result = $this->memoryStorage->ltrim('key', 13, 42, $this->options);
        $this->assertEquals($result, 'OK');
    }

    public function testMget() {
        $this->SetupMemoryStorageCommand(
            'mget',
            'GET',
            '/ms/_mget',
            [],
            ['keys' => 'foo,bar,baz'],
            ['Excuse me', 'while I', 'kiss the sky']
        );

        $result = $this->memoryStorage->mget(['foo', 'bar', 'baz'], $this->options);
        $this->assertEquals($result, ['Excuse me', 'while I', 'kiss the sky']);
    }

    public function testMset() {
        $entries = [
            ['key' => 'field1', 'value' => 'foo'],
            ['key' => 'field2', 'value' => 'bar'],
            ['key' => '...', 'value' => '...']
        ];

        $this->SetupMemoryStorageCommand(
            'mset',
            'POST',
            '/ms/_mset',
            ['body' => ['entries' => $entries]],
            [],
            'OK'
        );

        $result = $this->memoryStorage->mset($entries, $this->options);
        $this->assertEquals($result, 'OK');
    }

    public function testMsetnx() {
        $entries = [
            ['key' => 'field1', 'value' => 'foo'],
            ['key' => 'field2', 'value' => 'bar'],
            ['key' => '...', 'value' => '...']
        ];

        $this->SetupMemoryStorageCommand(
            'msetnx',
            'POST',
            '/ms/_msetnx',
            ['body' => ['entries' => $entries]],
            [],
            'OK'
        );

        $result = $this->memoryStorage->msetnx($entries, $this->options);
        $this->assertEquals($result, 'OK');
    }

    public function testObject() {
        $this->SetupMemoryStorageCommand(
            'object',
            'GET',
            '/ms/_object/key',
            ['_id' => 'key'],
            ['subcommand' => 'refcount'],
            'foobar'
        );

        $result = $this->memoryStorage->object('key', 'refcount', $this->options);
        $this->assertEquals($result, 'foobar');
    }

    public function testPersist() {
        $this->SetupMemoryStorageCommand(
            'persist',
            'POST',
            '/ms/_persist/key',
            ['_id' => 'key'],
            [],
            1
        );

        $result = $this->memoryStorage->persist('key', $this->options);
        $this->assertEquals($result, 1);
    }

    public function testPexpire() {
        $this->SetupMemoryStorageCommand('pexpire', 'POST', '/ms/_pexpire/key', [
            '_id' => 'key',
            'body' => ['milliseconds' => 42000]
        ], [], 0);

        $result = $this->memoryStorage->pexpire('key', 42000, $this->options);
        $this->assertEquals($result, 0);
    }

    public function testPexpireat() {
        $this->SetupMemoryStorageCommand('pexpireat', 'POST', '/ms/_pexpireat/key', [
            '_id' => 'key',
            'body' => ['timestamp' => 1234567890000]
        ], [], 1);

        $result = $this->memoryStorage->pexpireat('key', 1234567890000, $this->options);
        $this->assertEquals($result, 1);
    }

    public function testZrange()
    {
        $this->SetupMemoryStorageCommand(
            'zrange',
            'GET',
            '/ms/_zrange/key',
            ['_id' => 'key'],
            ['start' => 10, 'stop' => 15, 'options' => ['withscores'], 'limit' => '12,42'],
            ['foo', 1, 'bar', 2, 'baz', 3]);

        $this->options['limit'] = [12, 42];
        $result = $this->memoryStorage->zrange('key', 10, 15, $this->options);

        $this->assertEquals([
            ['member' => 'foo', 'score' => 1],
            ['member' => 'bar', 'score' => 2],
            ['member' => 'baz', 'score' => 3]
        ], $result);
    }
}
