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
                'volatile' => [],
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
        $this->SetupMemoryStorageCommand(
            'append',
            'POST',
            '/ms/_append/key',
            ['_id' => 'key', 'body' => ['value' => 'foo']],
            [],
            3
        );

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
        $this->assertEquals($result, false);
    }

    public function testExpireat() {
        $this->SetupMemoryStorageCommand('expireat', 'POST', '/ms/_expireat/key', [
            '_id' => 'key',
            'body' => ['timestamp' => 1234567890]
        ], [], 1);

        $result = $this->memoryStorage->expireat('key', 1234567890, $this->options);
        $this->assertEquals($result, true);
    }

    public function testFlushdb() {
        $this->SetupMemoryStorageCommand('flushdb', 'POST', '/ms/_flushdb' , [], [], 1);

        $result = $this->memoryStorage->flushdb($this->options);
        $this->assertEquals($result, null);
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
        $this->assertEquals($result, true);
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
        $this->assertEquals($result, null);
    }

    public function testHscan() {
        $this->SetupMemoryStorageCommand(
            'hscan',
            'GET',
            '/ms/_hscan/key',
            ['_id' => 'key'],
            ['cursor' => 0, 'count' => 10, 'match' => 'foo*'],
            [18, ['foo', 'bar', 'baz', 'qux']]
        );

        $this->options['count'] = 10;
        $this->options['match'] = 'foo*';

        $result = $this->memoryStorage->hscan('key', 0, $this->options);
        $this->assertEquals($result, ['cursor' => 18, 'values' => ['foo', 'bar', 'baz', 'qux']]);
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
        $this->assertEquals($result, true);
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
        $this->assertEquals($result, true);
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
        $this->assertEquals($result, null);
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
        $this->assertEquals($result, null);
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
        $this->assertEquals($result, null);
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
            0
        );

        $result = $this->memoryStorage->msetnx($entries, $this->options);
        $this->assertEquals($result, false);
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
        $this->assertEquals($result, true);
    }

    public function testPexpire() {
        $this->SetupMemoryStorageCommand('pexpire', 'POST', '/ms/_pexpire/key', [
            '_id' => 'key',
            'body' => ['milliseconds' => 42000]
        ], [], 0);

        $result = $this->memoryStorage->pexpire('key', 42000, $this->options);
        $this->assertEquals($result, false);
    }

    public function testPexpireat() {
        $this->SetupMemoryStorageCommand('pexpireat', 'POST', '/ms/_pexpireat/key', [
            '_id' => 'key',
            'body' => ['timestamp' => 1234567890000]
        ], [], 1);

        $result = $this->memoryStorage->pexpireat('key', 1234567890000, $this->options);
        $this->assertEquals($result, true);
    }

    public function testPfadd() {
        $this->SetupMemoryStorageCommand(
            'pfadd',
            'POST',
            '/ms/_pfadd/key',
            ['_id' => 'key', 'body' => ['elements' => ['foo', 'bar', 'baz']]],
            [],
            1
        );

        $result = $this->memoryStorage->pfadd('key', ['foo', 'bar', 'baz'], $this->options);
        $this->assertEquals($result, true);
    }

    public function testPfcount() {
        $this->SetupMemoryStorageCommand(
            'pfcount',
            'GET',
            '/ms/_pfcount',
            [],
            ['keys' => 'foo,bar,baz'],
            5
        );

        $result = $this->memoryStorage->pfcount(['foo', 'bar', 'baz'], $this->options);
        $this->assertEquals($result, 5);
    }

    public function testPfmerge() {
        $this->SetupMemoryStorageCommand(
            'pfmerge',
            'POST',
            '/ms/_pfmerge/key',
            ['_id' => 'key', 'body' => ['sources' => ['foo', 'bar', 'baz']]],
            [],
            'OK'
        );

        $result = $this->memoryStorage->pfmerge('key', ['foo', 'bar', 'baz'], $this->options);
        $this->assertEquals($result, null);
    }

    public function testPing() {
        $this->SetupMemoryStorageCommand('ping', 'GET', '/ms/_ping' , [], [], 'PONG');

        $result = $this->memoryStorage->ping($this->options);
        $this->assertEquals($result, 'PONG');
    }

    public function testPsetex() {
        $this->SetupMemoryStorageCommand(
            'psetex',
            'POST',
            '/ms/_psetex/key',
            ['_id' => 'key', 'body' => ['milliseconds' => 42000, 'value' => 'foo']],
            [],
            'OK'
        );

        $result = $this->memoryStorage->psetex('key', 'foo', 42000, $this->options);
        $this->assertEquals($result, null);
    }

    public function testPttl() {
        $this->SetupMemoryStorageCommand(
            'pttl',
            'GET',
            '/ms/_pttl/key',
            ['_id' => 'key'],
            [],
            43159
        );

        $result = $this->memoryStorage->pttl('key', $this->options);
        $this->assertEquals($result, 43159);
    }

    public function testRandomkey() {
        $this->SetupMemoryStorageCommand(
            'randomkey',
            'GET',
            '/ms/_randomkey',
            [],
            [],
            'foobar'
        );

        $result = $this->memoryStorage->randomkey($this->options);
        $this->assertEquals($result, 'foobar');
    }

    public function testRename() {
        $this->SetupMemoryStorageCommand(
            'rename',
            'POST',
            '/ms/_rename/key',
            ['_id' => 'key', 'body' => ['newkey' => 'foo']],
            [],
            'OK'
        );

        $result = $this->memoryStorage->rename('key', 'foo', $this->options);
        $this->assertEquals($result, null);
    }

    public function testRenamenx() {
        $this->SetupMemoryStorageCommand(
            'renamenx',
            'POST',
            '/ms/_renamenx/key',
            ['_id' => 'key', 'body' => ['newkey' => 'foo']],
            [],
            'OK'
        );

        $result = $this->memoryStorage->renamenx('key', 'foo', $this->options);
        $this->assertEquals($result, null);
    }

    public function testRpop() {
        $this->SetupMemoryStorageCommand(
            'rpop',
            'POST',
            '/ms/_rpop/key',
            ['_id' => 'key'],
            [],
            'foobar'
        );

        $result = $this->memoryStorage->rpop('key', $this->options);
        $this->assertEquals($result, 'foobar');
    }

    public function testRpoplpush() {
        $this->SetupMemoryStorageCommand(
            'rpoplpush',
            'POST',
            '/ms/_rpoplpush',
            ['body' => ['source' => 'foo', 'destination' => 'bar']],
            [],
            'foobar'
        );

        $result = $this->memoryStorage->rpoplpush('foo', 'bar', $this->options);
        $this->assertEquals($result, 'foobar');
    }

    public function testRpush() {
        $this->SetupMemoryStorageCommand(
            'rpush',
            'POST',
            '/ms/_rpush/key',
            ['_id' => 'key', 'body' => ['values' => ['foo', 'bar', 'baz']]],
            [],
            8
        );

        $result = $this->memoryStorage->rpush('key', ['foo', 'bar', 'baz'], $this->options);
        $this->assertEquals($result, 8);
    }

    public function testRpushx() {
        $this->SetupMemoryStorageCommand(
            'rpushx',
            'POST',
            '/ms/_rpushx/key',
            ['_id' => 'key', 'body' => ['value' => 'foobar']],
            [],
            8
        );

        $result = $this->memoryStorage->rpushx('key', 'foobar', $this->options);
        $this->assertEquals($result, 8);
    }

    public function testSadd() {
        $this->SetupMemoryStorageCommand(
            'sadd',
            'POST',
            '/ms/_sadd/key',
            ['_id' => 'key', 'body' => ['members' => ['foo', 'bar', 'baz']]],
            [],
            8
        );

        $result = $this->memoryStorage->sadd('key', ['foo', 'bar', 'baz'], $this->options);
        $this->assertEquals($result, 8);
    }

    public function testScan() {
        $this->SetupMemoryStorageCommand(
            'scan',
            'GET',
            '/ms/_scan',
            [],
            ['cursor' => 0, 'count' => 10, 'match' => 'foo*'],
            [18, ['foo', 'bar', 'baz', 'qux']]
        );

        $this->options['count'] = 10;
        $this->options['match'] = 'foo*';

        $result = $this->memoryStorage->scan(0, $this->options);
        $this->assertEquals($result, ['cursor' => 18, 'values' => ['foo', 'bar', 'baz', 'qux']]);
    }

    public function testScard() {
        $this->SetupMemoryStorageCommand(
            'scard',
            'GET',
            '/ms/_scard/key',
            ['_id' => 'key'],
            [],
            5
        );

        $result = $this->memoryStorage->scard('key', $this->options);
        $this->assertEquals($result, 5);
    }

    public function testSdiff() {
        $this->SetupMemoryStorageCommand(
            'sdiff',
            'GET',
            '/ms/_sdiff/key',
            ['_id' => 'key'],
            ['keys' => 'foo,bar,baz'],
            ['Excuse me', 'while I', 'kiss the sky']
        );

        $result = $this->memoryStorage->sdiff('key', ['foo', 'bar', 'baz'], $this->options);
        $this->assertEquals($result, ['Excuse me', 'while I', 'kiss the sky']);
    }

    public function testSdiffstore() {
        $this->SetupMemoryStorageCommand(
            'sdiffstore',
            'POST',
            '/ms/_sdiffstore/key',
            ['_id' => 'key', 'body' => ['keys' => ['foo', 'bar', 'baz'], 'destination' => 'foobar']],
            [],
            3
        );

        $result = $this->memoryStorage->sdiffstore('key', ['foo', 'bar', 'baz'], 'foobar', $this->options);
        $this->assertEquals($result, 3);
    }

    public function testSet() {
        $this->SetupMemoryStorageCommand(
            'set',
            'POST',
            '/ms/_set/key',
            [
                '_id' => 'key',
                'body' => [
                    'value' => 'foobar',
                    'ex' => 42,
                    'px' => 42000,
                    'nx' => true,
                    'xx' => true
                ]
            ],
            [],
            'OK'
        );

        $this->options['ex'] = 42;
        $this->options['px'] = 42000;
        $this->options['nx'] = true;
        $this->options['xx'] = true;

        $result = $this->memoryStorage->set('key', 'foobar', $this->options);
        $this->assertEquals($result, null);
    }

    public function testSetex() {
        $this->SetupMemoryStorageCommand(
            'setex',
            'POST',
            '/ms/_setex/key',
            ['_id' => 'key','body' => ['value' => 'foobar', 'seconds' => 42]],
            [],
            'OK'
        );

        $result = $this->memoryStorage->setex('key', 'foobar', 42, $this->options);
        $this->assertEquals($result, null);
    }

    public function testSetnx() {
        $this->SetupMemoryStorageCommand(
            'setnx',
            'POST',
            '/ms/_setnx/key',
            ['_id' => 'key','body' => ['value' => 'foobar']],
            [],
            1
        );

        $result = $this->memoryStorage->setnx('key', 'foobar', $this->options);
        $this->assertEquals($result, true);
    }

    public function testSinter() {
        $this->SetupMemoryStorageCommand(
            'sinter',
            'GET',
            '/ms/_sinter',
            [],
            ['keys' => 'foo,bar,baz'],
            ['Excuse me', 'while I', 'kiss the sky']
        );

        $result = $this->memoryStorage->sinter(['foo', 'bar', 'baz'], $this->options);
        $this->assertEquals($result, ['Excuse me', 'while I', 'kiss the sky']);
    }

    public function testSinterstore() {
        $this->SetupMemoryStorageCommand(
            'sinterstore',
            'POST',
            '/ms/_sinterstore',
            ['body' => ['keys' => ['foo', 'bar', 'baz'], 'destination' => 'foobar']],
            [],
            3
        );

        $result = $this->memoryStorage->sinterstore('foobar', ['foo', 'bar', 'baz'], $this->options);
        $this->assertEquals($result, 3);
    }

    public function testSismember() {
        $this->SetupMemoryStorageCommand(
            'sismember',
            'GET',
            '/ms/_sismember/key/foobar',
            ['_id' => 'key'],
            [],
            1
        );

        $result = $this->memoryStorage->sismember('key', 'foobar', $this->options);
        $this->assertEquals($result, true);
    }

    public function testSmembers() {
        $this->SetupMemoryStorageCommand(
            'smembers',
            'GET',
            '/ms/_smembers/key',
            ['_id' => 'key'],
            [],
            ['foo', 'bar', 'baz']
        );

        $result = $this->memoryStorage->smembers('key', $this->options);
        $this->assertEquals($result, ['foo', 'bar', 'baz']);
    }

    public function testSmove() {
        $this->SetupMemoryStorageCommand(
            'smove',
            'POST',
            '/ms/_smove/key',
            ['_id' => 'key', 'body' => ['destination' => 'foo', 'member' => 'bar']],
            [],
            1
        );

        $result = $this->memoryStorage->smove('key', 'foo', 'bar', $this->options);
        $this->assertEquals($result, true);
    }

    public function testSort() {
        $this->SetupMemoryStorageCommand(
            'sort',
            'POST',
            '/ms/_sort/key',
            [
                '_id' => 'key',
                'body' => [
                    'alpha' => true,
                    'by' => 'foo*',
                    'direction' => 'desc',
                    'get' => ['foo', 'bar'],
                    'limit' => ['offset' => 13, 'count' => 42]
                ]
            ],
            [],
            ['foo', 'bar', 'baz']
        );

        $this->options['alpha'] = true;
        $this->options['by'] = 'foo*';
        $this->options['direction'] = 'desc';
        $this->options['get'] = ['foo', 'bar'];
        $this->options['limit'] = ['offset' => 13, 'count' => 42];

        $result = $this->memoryStorage->sort('key', $this->options);
        $this->assertEquals($result, ['foo', 'bar', 'baz']);
    }

    public function testSpop() {
        $this->SetupMemoryStorageCommand(
            'spop',
            'POST',
            '/ms/_spop/key',
            ['_id' => 'key', 'body' => ['count' => 42]],
            [],
            'foo'
        );

        $this->options['count'] = 42;

        $result = $this->memoryStorage->spop('key', $this->options);
        $this->assertEquals($result, ['foo']);
    }

    public function testSrandmember() {
        $this->SetupMemoryStorageCommand(
            'srandmember',
            'GET',
            '/ms/_srandmember/key',
            ['_id' => 'key'],
            ['count' => 42],
            'foo'
        );

        $this->options['count'] = 42;

        $result = $this->memoryStorage->srandmember('key', $this->options);
        $this->assertEquals($result, ['foo']);
    }

    public function testSrem() {
        $this->SetupMemoryStorageCommand(
            'srem',
            'DELETE',
            '/ms/_srem/key',
            ['_id' => 'key', 'body' => ['members' => ['foo', 'bar', 'baz']]],
            [],
            3
        );

        $result = $this->memoryStorage->srem('key', ['foo', 'bar', 'baz'], $this->options);
        $this->assertEquals($result, 3);
    }

    public function testSscan() {
        $this->SetupMemoryStorageCommand(
            'sscan',
            'GET',
            '/ms/_sscan/key',
            ['_id' => 'key'],
            ['cursor' => 0, 'count' => 10, 'match' => 'foo*'],
            [18, ['foo', 'bar', 'baz', 'qux']]
        );

        $this->options['count'] = 10;
        $this->options['match'] = 'foo*';

        $result = $this->memoryStorage->sscan('key', 0, $this->options);
        $this->assertEquals($result, ['cursor' => 18, 'values' => ['foo', 'bar', 'baz', 'qux']]);
    }

    public function testStrlen() {
        $this->SetupMemoryStorageCommand(
            'strlen',
            'GET',
            '/ms/_strlen/key',
            ['_id' => 'key'],
            [],
            5
        );

        $result = $this->memoryStorage->strlen('key', $this->options);
        $this->assertEquals($result, 5);
    }

    public function testSunion() {
        $this->SetupMemoryStorageCommand(
            'sunion',
            'GET',
            '/ms/_sunion',
            [],
            ['keys' => 'foo,bar,baz'],
            ['Excuse me', 'while I', 'kiss the sky']
        );

        $result = $this->memoryStorage->sunion(['foo', 'bar', 'baz'], $this->options);
        $this->assertEquals($result, ['Excuse me', 'while I', 'kiss the sky']);
    }

    public function testSunionstore() {
        $this->SetupMemoryStorageCommand(
            'sunionstore',
            'POST',
            '/ms/_sunionstore',
            ['body' => ['keys' => ['foo', 'bar', 'baz'], 'destination' => 'foobar']],
            [],
            3
        );

        $result = $this->memoryStorage->sunionstore('foobar', ['foo', 'bar', 'baz'], $this->options);
        $this->assertEquals($result, 3);
    }

    public function testTime() {
        $this->SetupMemoryStorageCommand('time', 'GET', '/ms/_time' , [], [], [1234657890, 42]);

        $result = $this->memoryStorage->time($this->options);
        $this->assertEquals($result, [1234657890, 42]);
    }

    public function testTouch() {
        $this->SetupMemoryStorageCommand(
            'touch',
            'POST',
            '/ms/_touch',
            ['body' => ['keys' => ['foo', 'bar', 'baz']]],
            [],
            3
        );

        $result = $this->memoryStorage->touch(['foo', 'bar', 'baz'], $this->options);
        $this->assertEquals($result, 3);
    }

    public function testTtl() {
        $this->SetupMemoryStorageCommand(
            'ttl',
            'GET',
            '/ms/_ttl/key',
            ['_id' => 'key'],
            [],
            42
        );

        $result = $this->memoryStorage->ttl('key', $this->options);
        $this->assertEquals($result, 42);
    }

    public function testType() {
        $this->SetupMemoryStorageCommand(
            'type',
            'GET',
            '/ms/_type/key',
            ['_id' => 'key'],
            [],
            'zset'
        );

        $result = $this->memoryStorage->type('key', $this->options);
        $this->assertEquals($result, 'zset');
    }

    public function testZadd() {
        $elements = [
            ['score' => 1, 'member' => 'foo'],
            ['score' => 2, 'member' => 'bar'],
            ['score' => 3, 'member' => 'baz']
        ];

        $this->SetupMemoryStorageCommand(
            'zadd',
            'POST',
            '/ms/_zadd/key',
            [
                '_id' => 'key',
                'body' => [
                    'elements' => $elements,
                    'nx' => true,
                    'xx' => false,
                    'ch' => true,
                    'incr' => false
                ]
            ],
            [],
            8
        );

        $this->options['nx'] = true;
        $this->options['xx'] = false;
        $this->options['ch'] = true;
        $this->options['incr'] = false;

        $result = $this->memoryStorage->zadd('key', $elements, $this->options);
        $this->assertEquals($result, 8);
    }

    public function testZcard() {
        $this->SetupMemoryStorageCommand(
            'zcard',
            'GET',
            '/ms/_zcard/key',
            ['_id' => 'key'],
            [],
            5
        );

        $result = $this->memoryStorage->zcard('key', $this->options);
        $this->assertEquals($result, 5);
    }

    public function testZcount() {
        $this->SetupMemoryStorageCommand(
            'zcount',
            'GET',
            '/ms/_zcount/key',
            ['_id' => 'key'],
            ['min' => 10, 'max' => 42],
            5
        );

        $result = $this->memoryStorage->zcount('key', 10, 42, $this->options);
        $this->assertEquals($result, 5);
    }

    public function testZincrby() {
        $this->SetupMemoryStorageCommand(
            'zincrby',
            'POST',
            '/ms/_zincrby/key',
            ['_id' => 'key', 'body' => ['member' => 'foo', 'value' => 42]],
            [],
            50
        );

        $result = $this->memoryStorage->zincrby('key', 'foo', 42, $this->options);
        $this->assertEquals($result, 50);
    }

    public function testZinterstore() {
        $this->SetupMemoryStorageCommand(
            'zinterstore',
            'POST',
            '/ms/_zinterstore/key',
            [
                '_id' => 'key',
                'body' => [
                    'keys' => ['foo', 'bar', 'baz'],
                    'weights' => [10, 20, 30],
                    'aggregate' => 'sum'
                ]
            ],
            [],
            3
        );

        $this->options['weights'] = [10, 20, 30];
        $this->options['aggregate'] = 'sum';

        $result = $this->memoryStorage->zinterstore('key', ['foo', 'bar', 'baz'], $this->options);
        $this->assertEquals($result, 3);
    }

    public function testZlexcount() {
        $this->SetupMemoryStorageCommand(
            'zlexcount',
            'GET',
            '/ms/_zlexcount/key',
            ['_id' => 'key'],
            ['min' => 10, 'max' => 42],
            5
        );

        $result = $this->memoryStorage->zlexcount('key', 10, 42, $this->options);
        $this->assertEquals($result, 5);
    }

    public function testZrange()
    {
        $this->SetupMemoryStorageCommand(
            'zrange',
            'GET',
            '/ms/_zrange/key',
            ['_id' => 'key'],
            ['start' => 10, 'stop' => 15, 'options' => 'withscores', 'limit' => '12,42'],
            ['foo', 1, 'bar', 2, 'baz', 3]);

        $this->options['limit'] = [12, 42];
        $result = $this->memoryStorage->zrange('key', 10, 15, $this->options);

        $this->assertEquals([
            ['member' => 'foo', 'score' => 1],
            ['member' => 'bar', 'score' => 2],
            ['member' => 'baz', 'score' => 3]
        ], $result);
    }

    public function testZrangebylex() {
        $this->SetupMemoryStorageCommand(
            'zrangebylex',
            'GET',
            '/ms/_zrangebylex/key',
            ['_id' => 'key'],
            ['min' => 10, 'max' => 42, 'limit' => '10,42'],
            ['foo', 'bar', 'baz']
        );

        $this->options['limit'] = [10, 42];


        $result = $this->memoryStorage->zrangebylex('key', 10, 42, $this->options);
        $this->assertEquals($result, ['foo', 'bar', 'baz']);
    }

    public function testZrangebyscore() {
        $this->SetupMemoryStorageCommand(
            'zrangebyscore',
            'GET',
            '/ms/_zrangebyscore/key',
            ['_id' => 'key'],
            ['min' => 10, 'max' => 42, 'limit' => '10,42', 'options' => 'withscores'],
            ['foo', 1, 'bar', 2, 'baz', 3]
        );

        $this->options['limit'] = [10, 42];


        $result = $this->memoryStorage->zrangebyscore('key', 10, 42, $this->options);
        $this->assertEquals($result, [
            ['member' => 'foo', 'score' => 1],
            ['member' => 'bar', 'score' => 2],
            ['member' => 'baz', 'score' => 3]
        ]);
    }

    public function testZrank() {
        $this->SetupMemoryStorageCommand(
            'zrank',
            'GET',
            '/ms/_zrank/key/foobar',
            ['_id' => 'key'],
            [],
            1
        );

        $result = $this->memoryStorage->zrank('key', 'foobar', $this->options);
        $this->assertEquals($result, 1);
    }

    public function testZrem() {
        $this->SetupMemoryStorageCommand(
            'zrem',
            'DELETE',
            '/ms/_zrem/key',
            ['_id' => 'key', 'body' => ['members' => ['foo', 'bar', 'baz']]],
            [],
            3
        );

        $result = $this->memoryStorage->zrem('key', ['foo', 'bar', 'baz'], $this->options);
        $this->assertEquals($result, 3);
    }

    public function testZremrangebylex() {
        $this->SetupMemoryStorageCommand(
            'zremrangebylex',
            'DELETE',
            '/ms/_zremrangebylex/key',
            ['_id' => 'key', 'body' => ['min' => 10, 'max' => 42]],
            [],
            3
        );

        $result = $this->memoryStorage->zremrangebylex('key', 10, 42, $this->options);
        $this->assertEquals($result, 3);
    }

    public function testZremrangebyrank() {
        $this->SetupMemoryStorageCommand(
            'zremrangebyrank',
            'DELETE',
            '/ms/_zremrangebyrank/key',
            ['_id' => 'key', 'body' => ['start' => 10, 'stop' => 42]],
            [],
            3
        );

        $result = $this->memoryStorage->zremrangebyrank('key', 10, 42, $this->options);
        $this->assertEquals($result, 3);
    }

    public function testZremrangebyscore() {
        $this->SetupMemoryStorageCommand(
            'zremrangebyscore',
            'DELETE',
            '/ms/_zremrangebyscore/key',
            ['_id' => 'key', 'body' => ['min' => 10, 'max' => 42]],
            [],
            3
        );

        $result = $this->memoryStorage->zremrangebyscore('key', 10, 42, $this->options);
        $this->assertEquals($result, 3);
    }

    public function testZrevrange()
    {
        $this->SetupMemoryStorageCommand(
            'zrevrange',
            'GET',
            '/ms/_zrevrange/key',
            ['_id' => 'key'],
            ['start' => 10, 'stop' => 15, 'options' => 'withscores', 'limit' => '12,42'],
            ['foo', 1, 'bar', 2, 'baz', 3]);

        $this->options['limit'] = [12, 42];
        $result = $this->memoryStorage->zrevrange('key', 10, 15, $this->options);

        $this->assertEquals([
            ['member' => 'foo', 'score' => 1],
            ['member' => 'bar', 'score' => 2],
            ['member' => 'baz', 'score' => 3]
        ], $result);
    }

    public function testZrevrangebylex() {
        $this->SetupMemoryStorageCommand(
            'zrevrangebylex',
            'GET',
            '/ms/_zrevrangebylex/key',
            ['_id' => 'key'],
            ['min' => 10, 'max' => 42, 'limit' => '10,42'],
            ['foo', 'bar', 'baz']
        );

        $this->options['limit'] = [10, 42];


        $result = $this->memoryStorage->zrevrangebylex('key', 10, 42, $this->options);
        $this->assertEquals($result, ['foo', 'bar', 'baz']);
    }

    public function testZrevrangebyscore() {
        $this->SetupMemoryStorageCommand(
            'zrevrangebyscore',
            'GET',
            '/ms/_zrevrangebyscore/key',
            ['_id' => 'key'],
            ['min' => 10, 'max' => 42, 'limit' => '10,42', 'options' => 'withscores'],
            ['foo', 1, 'bar', 2, 'baz', 3]
        );

        $this->options['limit'] = [10, 42];


        $result = $this->memoryStorage->zrevrangebyscore('key', 10, 42, $this->options);
        $this->assertEquals($result, [
            ['member' => 'foo', 'score' => 1],
            ['member' => 'bar', 'score' => 2],
            ['member' => 'baz', 'score' => 3]
        ]);
    }

    public function testZrevrank() {
        $this->SetupMemoryStorageCommand(
            'zrevrank',
            'GET',
            '/ms/_zrevrank/key/foobar',
            ['_id' => 'key'],
            [],
            1
        );

        $result = $this->memoryStorage->zrevrank('key', 'foobar', $this->options);
        $this->assertEquals($result, 1);
    }

    public function testZscan() {
        $this->SetupMemoryStorageCommand(
            'zscan',
            'GET',
            '/ms/_zscan/key',
            ['_id' => 'key'],
            ['cursor' => 0, 'count' => 10, 'match' => 'foo*'],
            [18, ['foo', 'bar', 'baz', 'qux']]
        );

        $this->options['count'] = 10;
        $this->options['match'] = 'foo*';

        $result = $this->memoryStorage->zscan('key', 0, $this->options);
        $this->assertEquals($result, ['cursor' => 18, 'values' => ['foo', 'bar', 'baz', 'qux']]);
    }

    public function testZscore() {
        $this->SetupMemoryStorageCommand(
            'zscore',
            'GET',
            '/ms/_zscore/key/foobar',
            ['_id' => 'key'],
            [],
            '3.14159'
        );

        $result = $this->memoryStorage->zscore('key', 'foobar', $this->options);
        $this->assertEquals($result, 3.14159);
    }

    public function testZunionstore() {
        $this->SetupMemoryStorageCommand(
            'zunionstore',
            'POST',
            '/ms/_zunionstore/key',
            [
                '_id' => 'key',
                'body' => [
                    'keys' => ['foo', 'bar', 'baz'],
                    'weights' => [10, 20, 30],
                    'aggregate' => 'sum'
                ]
            ],
            [],
            3
        );

        $this->options['weights'] = [10, 20, 30];
        $this->options['aggregate'] = 'sum';

        $result = $this->memoryStorage->zunionstore('key', ['foo', 'bar', 'baz'], $this->options);
        $this->assertEquals($result, 3);
    }
}
