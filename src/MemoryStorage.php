<?php

namespace Kuzzle;

use InvalidArgumentException;

/**
 * Class MemoryStorage
 * @package kuzzleio/kuzzle-sdk
 *
 * @method append
 * @method bitcount
 * @method bitop
 * @method bitpos
 * @method dbsize
 * @method decr
 * @method decrby
 * @method del
 * @method exists
 * @method expire
 * @method expireat
 * @method flushdb
 * @method geoadd
 * @method geodist
 * @method geohash
 * @method geopos
 * @method georadius
 * @method georadiusbymember
 * @method get
 * @method getbit
 * @method getrange
 * @method getset
 * @method hdel
 * @method hexists
 * @method hget
 * @method hgetall
 * @method hincrby
 * @method hincrbyfloat
 * @method hkeys
 * @method hlen
 * @method hmget
 * @method hmset
 * @method hscan
 * @method hset
 * @method hsetnx
 * @method hstrlen
 * @method hvals
 * @method incr
 * @method incrby
 * @method incrbyfloat
 * @method keys
 * @method lindex
 * @method linsert
 * @method llen
 * @method lpop
 * @method lpush
 * @method lpushx
 * @method lrange
 * @method lrem
 * @method lset
 * @method ltrim
 * @method mget
 * @method mset
 * @method msetnx
 * @method object
 * @method persist
 * @method pexpire
 * @method pexpireat
 * @method pfadd
 * @method pfcount
 * @method pfmerge
 * @method ping
 * @method psetex
 * @method pttl
 * @method randomkey
 * @method rename
 * @method renamenx
 * @method rpop
 * @method rpoplpush
 * @method rpush
 * @method rpushx
 * @method sadd
 * @method scan
 * @method scard
 * @method sdiff
 * @method sdiffstore
 * @method set
 * @method setex
 * @method setnx
 * @method sinter
 * @method sinterstore
 * @method sismember
 * @method smembers
 * @method smove
 * @method sort
 * @method spop
 * @method srandmember
 * @method srem
 * @method sscan
 * @method strlen
 * @method sunion
 * @method sunionstore
 * @method time
 * @method touch
 * @method ttl
 * @method type
 * @method zadd
 * @method zcard
 * @method zcount
 * @method zincrby
 * @method zinterstore
 * @method zlexcount
 * @method zrange
 * @method zrangebylex
 * @method zrangebyscore
 * @method zrank
 * @method zrem
 * @method zremrangebylex
 * @method zremrangebyscore
 * @method zrevrangebylex
 * @method zrevrange
 * @method zrevrangebyscore
 * @method zrevrank
 * @method zscan
 * @method zscore
 * @method zunionstore
 */
class MemoryStorage
{
    private $COMMANDS = [
        'append' => ['required' => ['_id', 'value']],
        'bitcount' => ['getter' => true, 'required' => ['_id'], 'opts' => ['start', 'end']],
        'bitop' => ['required' => ['_id', 'operation', 'keys']],
        'bitpos' => ['getter' => true, 'required' => ['_id', 'bit'], 'opts' => ['start', 'end']],
        'dbsize' => ['getter' => true],
        'decr' => ['required' => ['_id']],
        'decrby' => ['required' => ['_id', 'value']],
        'del' => ['required' => ['keys']],
        'exists' => ['getter' => true, 'required' => ['keys']],
        'expire' => ['required' => ['_id', 'seconds']],
        'expireat' => ['required' => ['_id', 'timestamp']],
        'flushdb' => [],
        'geoadd' => ['required' => ['_id', 'points']],
        'geodist' => [
            'getter' => true,
            'required' => ['_id', 'member1', 'member2'],
            'opts' => ['unit'],
            'mapResults' => 'floatval'
        ],
        'geohash' => ['getter' => true, 'required' => ['_id', 'members']],
        'geopos' => ['getter' => true, 'required' => ['_id', 'members'], 'mapResults' => 'mapGeoposResults'],
        'georadius' => [
            'getter' => true,
            'required' => ['_id', 'lon', 'lat', 'distance', 'unit'],
            'opts' => 'assignGeoRadiusOptions',
            'mapResults' => 'mapGeoRadiusResults'
        ],
        'georadiusbymember' => [
            'getter' => true,
            'required' => ['_id', 'member', 'distance', 'unit'],
            'opts' => 'assignGeoRadiusOptions',
            'mapResults' => 'mapGeoRadiusResults'
        ],
        'get' => ['getter' => true, 'required' => ['_id']],
        'getbit' => ['getter' => true, 'required' => ['_id', 'offset']],
        'getrange' => ['getter' => true, 'required' => ['_id', 'start', 'end']],
        'getset' => ['required' => ['_id', 'fields']],
        'hdel' => ['required' => ['_id', 'fields']],
        'hexists' => ['getter' => true, 'required' => ['_id', 'field']],
        'hget' => ['getter' => true, 'required' => ['_id', 'field']],
        'hgetall' => ['getter' => true, 'required' => ['_id'], 'mapResults' => 'mapKeyValueResults'],
        'hincrby' => ['required' => ['_id', 'field', 'value']],
        'hincrbyfloat' => ['required' => ['_id', 'field', 'value'], 'mapResults' => 'floatval'],
        'hkeys' => ['getter' => true, 'required' => ['_id']],
        'hlen' => ['getter' => true, 'required' => ['_id']],
        'hmget' => ['getter' => true, 'required' => ['_id', 'fields']],
        'hmset' => ['required' => ['_id', 'entries']],
        'hscan' => ['getter' => true, 'required' => ['_id', 'cursor'], 'opts' => ['match', 'count']],
        'hset' => ['required' => ['_id', 'field', 'value']],
        'hsetnx' => ['required' => ['_id', 'field', 'value']],
        'hstrlen' => ['getter' => true, 'required' => ['_id', 'field']],
        'hvals' => ['getter' => true, 'required' => ['_id']],
        'incr' => ['required' => ['_id']],
        'incrby' => ['required' => ['_id', 'value']],
        'incrbyfloat' => ['required' => ['_id', 'value']],
        'keys' => ['getter' => true, 'required' => ['pattern']],
        'lindex' => ['getter' => true, 'required' => ['_id', 'index']],
        'linsert' => ['required' => ['_id', 'position', 'pivot', 'value']],
        'llen' => ['getter' => true, 'required' => ['_id']],
        'lpop' => ['required' => ['_id']],
        'lpush' => ['required' => ['_id', 'values']],
        'lpushx' => ['required' => ['_id', 'value']],
        'lrange' => ['getter' => true, 'required' => ['_id', 'start', 'stop']],
        'lrem' => ['required' => ['_id', 'count', 'value']],
        'lset' => ['required' => ['_id', 'index', 'value']],
        'ltrim' => ['required' => ['_id', 'start', 'stop']],
        'mget' => ['getter' => true, 'required' => ['keys']],
        'mset' => ['required' => ['entries']],
        'msetnx' => ['required' => ['entries']],
        'object' => ['getter' => true, 'required' => ['_id', 'subcommand']],
        'persist' => ['required' => ['_id']],
        'pexpire' => ['required' => ['_id', 'milliseconds']],
        'pexpireat' => ['required' => ['_id', 'timestamp']],
        'pfadd' => ['required' => ['_id', 'elements']],
        'pfcount' => ['getter' => true, 'required' => ['keys']],
        'pfmerge' => ['required' => ['_id', 'sources']],
        'ping' => ['getter' => true],
        'psetex' => ['required' => ['_id', 'value', 'milliseconds']],
        'pttl' => ['getter' => true, 'required' => ['_id']],
        'randomkey' => ['getter' => true],
        'rename' => ['required' => ['_id', 'newkey']],
        'renamenx' => ['required' => ['_id', 'newkey']],
        'rpop' => ['required' => ['_id']],
        'rpoplpush' => ['required' => ['source', 'destination']],
        'rpush' => ['required' => ['_id', 'values']],
        'rpushx' => ['required' => ['_id', 'value']],
        'sadd' => ['required' => ['_id', 'members']],
        'scan' => ['getter' => true, 'required' => ['cursor'], 'opts' => ['match', 'count']],
        'scard' => ['getter' => true, 'required' => ['_id']],
        'sdiff' => ['getter' => true, 'required' => ['_id', 'keys']],
        'sdiffstore' => ['required' => ['_id', 'keys', 'destination']],
        'set' => ['required' => ['_id', 'value'], 'opts' => ['ex', 'px', 'nx', 'xx']],
        'setex' => ['required' => ['_id', 'value', 'seconds']],
        'setnx' => ['required' => ['_id', 'value']],
        'sinter' => ['getter' => true, 'required' => ['keys']],
        'sinterstore' => ['required' => ['destination', 'keys']],
        'sismember' => ['required' => ['_id', 'member']],
        'smembers' => ['getter' => true, 'required' => ['_id']],
        'smove' => ['required' => ['_id', 'destination', 'member']],
        'sort' => ['getter' => true, 'required' => ['_id'], 'opts' => ['alpha', 'by', 'direction', 'get', 'limit']],
        'spop' => ['required' => ['_id'], 'mapResults' => 'mapStringToArray'],
        'srandmember' => ['getter' => true, 'required' => ['_id'], 'opts' => ['count'], 'mapResults' => 'mapStringToArray'],
        'srem' => ['required' => ['_id', 'members']],
        'sscan' => ['getter' => true, 'required' => ['_id', 'cursor'], 'opts' => ['match', 'count']],
        'strlen' => ['getter' => true, 'required' => ['_id']],
        'sunion' => ['getter' => true, 'required' => ['keys']],
        'sunionstore' => ['required' => ['destination', 'keys']],
        'time' => ['getter' => true, 'mapResults' => 'mapArrayStringToArrayInt'],
        'touch' => ['required' => 'keys'],
        'ttl' => ['getter' => 'true', 'required' => ['_id']],
        'type' => ['getter' => 'true', 'required' => ['_id']],
        'zadd' => ['required' => ['_id', 'elements'], 'opts' => ['nx', 'xx', 'ch', 'incr']],
        'zcount' => ['getter' => true, 'required' => ['_id', 'min', 'max']],
        'zincrby' => ['required' => ['_id', 'member', 'value']],
        'zinterstore' => ['required' => ['_id', 'keys'], 'opts' => ['weights', 'aggregate']],
        'zlexcount' => ['getter' => true, 'required' => ['_id', 'min', 'max']],
        'zrange' => [
            'getter' => true,
            'required' => ['_id', 'start', 'stop'],
            'opts' => 'assignZrangeOptions',
            'mapResults' => 'mapZrangeResults'
        ],
        'zrangebylex' => ['getter' => true, 'required' => ['_id', 'min', 'max'], 'opts' => ['limit']],
        'zrangebyscore' => [
            'getter' => true,
            'required' => ['_id', 'min', 'max'],
            'opts' => 'assignZrangeOptions',
            'mapResults' => 'mapZrangeResults'
        ],
        'zrank' => ['getter' => true, 'required' => ['_id', 'member']],
        'zrem' => ['required' => ['_id', 'members']],
        'zremrangebylex' => ['required' => ['_id', 'min', 'max']],
        'zremrangebyrank' => ['required' => ['_id', 'start', 'stop']],
        'zremrangebyscore' => ['required' => ['_id', 'min', 'max']],
        'zrevrange' => [
            'getter' => true,
            'required' => ['_id', 'start', 'stop'],
            'opts' => 'assignZrangeOptions',
            'mapResults' => 'mapZrangeResults'
        ],
        'zrevangebylex' => ['getter' => true, 'required' => ['_id', 'min', 'max'], 'opts' => ['limit']],
        'zrevrangebyscore' => [
            'getter' => true,
            'required' => ['_id', 'min', 'max'],
            'opts' => 'assignZrangeOptions',
            'mapResults' => 'mapZrangeResults'
        ],
        'zrevrank' => ['getter' => true, 'required' => ['_id', 'member']],
        'zscan' => ['getter' => true, 'required' => ['_id', 'cursor'], 'opts' => ['match', 'count']],
        'zscore' => ['getter' => true, 'required' => ['_id', 'member']],
        'zunionstore' => ['required' => ['_id', 'keys'], 'opts' => ['weights', 'aggregate']]
    ];

    protected $kuzzle;

    public function __construct(Kuzzle $kuzzle)
    {
        $this->kuzzle = $kuzzle;
    }

    public function __call($command, $arguments)
    {
        if (!isset($this->COMMANDS[$command])) {
            throw new InvalidArgumentException('MemoryStorage: Command "' . $command . '" not found');
        }
        
        $data = [];
        $query = ['controller' => 'memoryStorage', 'action' => $command];
        $getter = false;
        
        if (isset($this->COMMANDS[$command]['getter']) && $this->COMMANDS[$command]['getter'] == true) {
            $getter = true;
            $data['body'] = [];
        }

        if (isset($this->COMMANDS[$command]['required'])) {
            foreach($this->COMMANDS[$command]['required'] as $key) {
                $value = array_shift($arguments);

                if ($value == null) {
                    throw new InvalidArgumentException('MemoryStorage.' . $command . ': Missing parameter "' . $key . '"');
                }

                $this->assignParameter($data, $getter, $key, $value);
            }
        }

        $argsLeft = count($arguments);

        if ($argsLeft > 1) {
            throw new InvalidArgumentException('MemoryStorage.' . $command . ': Too many parameters provided');
        }

        $options = [];

        if (isset($this->COMMANDS[$command]['opts'])) {
            if ($argsLeft == 1) {
                $options = array_shift($arguments);

                if (!is_array($options)) {
                    throw new InvalidArgumentException('MemoryStorage.' . $command . ': Invalid optional parameter (expected an associative array)');
                }

                if (is_array($this->COMMANDS[$command]['opts'])) {
                    foreach ($this->COMMANDS[$command]['opts'] as $opt) {
                        if (isset($options[$opt])) {
                            $this->assignParameter($data, $getter, $opt, $options[$opt]);
                            unset($options[$opt]);
                        }
                    }
                }
            }

            /*
               Options function mapper does not necessarily should be called
               whether options have been passed by the client or not
             */
            if (is_callable($this->COMMANDS[$command]['opts'])) {
                call_user_func([$this, $this->COMMANDS[$command]['opts']], $data, $options);
            }
        }

        if (!isset($options['httpParams'])) {
            $options['httpParams'] = [];
        }

        return $this->kuzzle->query($query, $data, $options);
    }

    /**
     * Assigns the $key => $value argument to the right
     * place in the $data structure
     *
     * Mutates $data
     *
     * @param $data
     * @param $getter
     * @param $key
     * @param $value
     */
    private function assignParameter(&$data, $getter, $key, $value) {
        if ($getter || $key == '_id') {
            $data[$key] = $value;
        }
        else {
            $data['body'][$key] = $value;
        }
    }

    /**
     * Assign the provided options for the georadius* redis functions
     * to the request object, as expected by Kuzzle API
     *
     * Mutates the provided data and options objects
     *
     * @param $data
     * @param $options
     */
    private function assignGeoRadiusOptions (&$data, &$options) {
        $parsed = [];

        $filtered = array_filter($options, function ($opt) {
            return array_search($opt, ['withcoord', 'withdist', 'count', 'sort']);
        });

        foreach($options as $opt) {
            if (array_search($opt, ['withcoord', 'withdist', 'count', 'sort'])) {
                if ($opt == 'withcoord' || $opt == 'withdist') {
                    array_push($parsed, $opt);
                } else if ($opt == 'count' || $opt == 'sort') {
                    if ($opt == 'count') {
                        array_push($parsed, 'count');
                    }

                    array_push($parsed, $options[$opt]);
                }

                unset($options[$opt]);
            }
        }

        if (count($parsed) > 0) {
            $data['options'] = $parsed;
        }
    }

    /**
     * Force the WITHSCORES option on z*range* routes
     *
     * Mutates the provided data and options objects
     *
     * @param data
     * @param options
     */
    private function assignZrangeOptions (&$data, &$options) {
        &$data['options'] = ['withscores'];

        if (isset($options['limit'])) {
            $data['limit'] = $options['limit'];
            unset($options['limit']);
        }
    }

    /**
     * Maps geopos results, from array<array<string>> to array<array<number>>
     *
     * @param results
     * @return array
     */
    private function mapGeoposResults($results) {
        $ret = [];

        foreach($results as $coords) {
            $point = [];

            foreach($coords as $latlon) {
                array_push($point, floatval($latlon));
            }

            array_push($ret, $point);
        }

        return $ret;
    }

    /**
     * Maps georadius results to the format specified in the SDK documentation,
     * preventing different formats depending on the passed options
     *
     * Results can be either an array of point names, or an array
     * of arrays, each one of them containing the point name,
     * and additional informations depending on the passed options
     * (coordinates, distances)
     *
     * @param results
     * @return array
     */
    private function mapGeoRadiusResults($results) {
        // Simple array of point names (no options provided)
        if (!is_array($results[0])) {
            return array_map(function (point) {
                    return ['name' => point];
            }, $results);
        }

        return results.map(function (point) {
                // The point id is always the first item
                var
                p = {
                    name: point[0]
      },
      i;

    for (i = 1; i < point.length; i++) {
        // withcoord result are in an array...
        if (Array.isArray(point[i])) {
            p.coordinates = point[i].map(function (coord) {
                    return parseFloat(coord);
                });
        }
        else {
            // ... and withdist are not
            p.distance = parseFloat(point[i]);
        }
    }

    return p;
  });
    }
}
