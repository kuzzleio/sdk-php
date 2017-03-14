<?php

namespace Kuzzle;

use InvalidArgumentException;

/**
 * Class MemoryStorage
 * @package kuzzleio/kuzzle-sdk
 *
 * @method int append(string $key, string $value, array $options = NULL)
 * @method int bitcount(string $key, $options = NULL)
 * @method int bitop(string $key, string $operation, array $keys, array $options = NULL)
 * @method int bitpos(string $key, int $bit, array $options = NULL)
 * @method int dbsize(array $options = NULL)
 * @method int decr(string $key, array $options = NULL)
 * @method int decrby(string $key, int $value, array $options = NULL)
 * @method int del(array $keys, array $options = NULL)
 * @method int exists(array $keys, array $options = NULL)
 * @method int expire(string $key, int $seconds, array $options = NULL)
 * @method int expireat(string $key, int $timestamp, array $options = NULL)
 * @method string flushdb(array $options = NULL)
 * @method int geoadd(string $key, array $points, array $options = NULL)
 * @method float geodist(string $key, string $member1, string $member2, array $options = NULL)
 * @method array geohash(string $key, array $members, array $options = NULL)
 * @method array geopos(string $key, array $members, array $options = NULL)
 * @method array georadius(string $key, float $longitude, float $latitude, float $distance, string $unit, array $options = NULL)
 * @method array georadiusbymember(string $key, string $member, float $distance, string $unit, array $options = NULL)
 * @method string get(string $key, array $options = NULL)
 * @method int getbit(string $key, int $offset, array $options = NULL)
 * @method string getrange(string $key, int $start, int $end, array $options = NULL)
 * @method string getset(string $key, string $value, array $options = NULL)
 * @method int hdel(string $key, array $fields, array $options = NULL)
 * @method int hexists(string $key, string $field, array $options = NULL)
 * @method string hget(string $key, string $field, array $options = NULL)
 * @method array hgetall(string $key, array $options = NULL)
 * @method int hincrby(string $key, string $field, int $value, array $options = NULL)
 * @method float hincrbyfloat(string $key, string $field, float $value, array $options = NULL)
 * @method array hkeys(string $key, array $options = NULL)
 * @method int hlen(string $key, array $options = NULL)
 * @method array hmget(string $key, array $fields, array $options = NULL)
 * @method string hmset(string $key, array $entries, array $options = NULL)
 * @method array hscan(string $key, int $cursor, array $options = NULL)
 * @method int hset(string $key, string $field, string $value, array $options = NULL)
 * @method int hsetnx(string $key, string $field, string $value, array $options = NULL)
 * @method int hstrlen(string $key, string $field, array $options = NULL)
 * @method array hvals(string $key, array $options = NULL)
 * @method int incr(string $key, array $options = NULL)
 * @method int incrby(string $key, int $value, array $options = NULL)
 * @method float incrbyfloat(string $key, float $value, array $options = NULL)
 * @method array keys(string $pattern, array $options = NULL)
 * @method string lindex(string $key, int $index, array $options = NULL)
 * @method int linsert(string $key, string $position, string $pivot, string $value, array $options = NULL)
 * @method int llen(string $key, array $options = NULL)
 * @method string lpop(string $key, array $options = NULL)
 * @method int lpush(string $key, array $values, array $options = NULL)
 * @method int lpushx(string $key, string $value, array $options = NULL)
 * @method array lrange(string $key, int $start, int $stop, array $options = NULL)
 * @method int lrem(string $key, int $count, string $value, array $options = NULL)
 * @method string lset(string $key, int $index, string $value, array $options = NULL)
 * @method string ltrim(string $key, int $start, int $stop, array $options = NULL)
 * @method array mget(array $keys, array $options = NULL)
 * @method string mset(array $entries, array $options = NULL)
 * @method int msetnx(array $entries, array $options = NULL)
 * @method string object(string $key, string $subcommand, array $options = NULL)
 * @method int persist(string $key, array $options = NULL)
 * @method int pexpire(string $key, int $milliseconds, array $options = NULL)
 * @method int pexpireat(string $key, int $timestamp, array $options = NULL)
 * @method int pfadd(string $key, array $elements, array $options = NULL)
 * @method int pfcount(array $keys, array $options = NULL)
 * @method string pfmerge(string $key, array $sources, array $options = NULL)
 * @method string ping(array $options = NULL)
 * @method string psetex(string $key, string $value, int $milliseconds, array $options = NULL)
 * @method int pttl(string $key, array $options = NULL)
 * @method string randomkey(array $options = NULL)
 * @method string rename(string $key, string $newkey, array $options = NULL)
 * @method string renamenx(string $key, string $newkey, array $options = NULL)
 * @method string rpop(string $key, array $options = NULL)
 * @method string rpoplpush(string $source, string $destination, array $options = NULL)
 * @method int rpush(string $key, array $values, array $options = NULL)
 * @method int rpushx(string $key, string $value, array $options = NULL)
 * @method int sadd(string $key, array $members, array $options = NULL)
 * @method array scan(int $cursor, array $options = NULL)
 * @method int scard(string $key, array $options = NULL)
 * @method array sdiff(string $key, array $keys, array $options = NULL)
 * @method int sdiffstore(string $key, array $keys, string $destination, array $options = NULL)
 * @method string set(string $key, string $value, array $options = NULL)
 * @method string setex(string $key, string $value, int $ttl, array $options = NULL)
 * @method int setnx(string $key, string $value, array $options = NULL)
 * @method array sinter(array $keys, array $options = NULL)
 * @method int sinterstore(string $destination, array $keys, array $options = NULL)
 * @method int sismember(string $key, string $member, array $options = NULL)
 * @method array smembers(string $key, array $options = NULL)
 * @method int smove(string $key, string $destination, string $member, array $options = NULL)
 * @method array sort(string $key, array $options = NULL)
 * @method array spop(string $key, array $options = NULL)
 * @method array srandmember(string $key, array $options = NULL)
 * @method int srem(string $key, array $members, array $options = NULL)
 * @method array sscan(string $key, int $cursor, array $options = NULL)
 * @method int strlen(string $key, array $options = NULL)
 * @method array sunion(array $keys, array $options = NULL)
 * @method int sunionstore(string $destination, array $keys, array $options = NULL)
 * @method array time(array $options = NULL)
 * @method int touch(array $keys, array $options = NULL)
 * @method int ttl(string $key, array $options = NULL)
 * @method string type(string $key, array $options = NULL)
 * @method int zadd(string $key, array $elements, array $options = NULL)
 * @method int zcard(string $key, array $options = NULL)
 * @method int zcount(string $key, int $min, int $max, array $options = NULL)
 * @method float zincrby(string $key, string $member, float $increment, array $options = NULL)
 * @method int zinterstore(string $destination, array $keys, array $options = NULL)
 * @method int zlexcount(string $key, int $min, int $max, array $options = NULL)
 * @method array zrange(string $key, int $start, int $stop, array $options = NULL)
 * @method array zrangebylex(string $key, int $min, int $max, array $options = NULL)
 * @method array zrangebyscore(string $key, int $min, int $max, array $options = NULL)
 * @method int zrank(string $key, string $member, array $options = NULL)
 * @method int zrem(string $key, array $members, array $options = NULL)
 * @method int zremrangebylex(string $key, int $min, int $max, array $options = NULL)
 * @method int zremrangebyrank(string $key, int $min, int $max, array $options = NULL)
 * @method int zremrangebyscore(string $key, int $min, int $max, array $options = NULL)
 * @method array zrevrange(string $key, int $start, int $stop, array $options = NULL)
 * @method array zrevrangebylex(string $key, int $min, int $max, array $options = NULL)
 * @method array zrevrangebyscore(string $key, int $min, int $max, array $options = NULL)
 * @method int zrevrank(string $key, string $member, array $options = NULL)
 * @method array zscan(string $key, int $cursor, array $options = NULL)
 * @method int zscore(string $key, string $member, array $options = NULL)
 * @method int zunionstore(string $destination, array $keys, array $options = NULL)
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
            'required' => ['_id', ':member1', ':member2'],
            'opts' => ['unit'],
            'mapResults' => 'mapStringToFloat'
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
        'getset' => ['required' => ['_id', 'value']],
        'hdel' => ['required' => ['_id', 'fields']],
        'hexists' => ['getter' => true, 'required' => ['_id', ':field']],
        'hget' => ['getter' => true, 'required' => ['_id', ':field']],
        'hgetall' => ['getter' => true, 'required' => ['_id']],
        'hincrby' => ['required' => ['_id', 'field', 'value']],
        'hincrbyfloat' => ['required' => ['_id', 'field', 'value'], 'mapResults' => 'mapStringToFloat'],
        'hkeys' => ['getter' => true, 'required' => ['_id']],
        'hlen' => ['getter' => true, 'required' => ['_id']],
        'hmget' => ['getter' => true, 'required' => ['_id', 'fields']],
        'hmset' => ['required' => ['_id', 'entries']],
        'hscan' => ['getter' => true, 'required' => ['_id', 'cursor'], 'opts' => ['match', 'count']],
        'hset' => ['required' => ['_id', 'field', 'value']],
        'hsetnx' => ['required' => ['_id', 'field', 'value']],
        'hstrlen' => ['getter' => true, 'required' => ['_id', ':field']],
        'hvals' => ['getter' => true, 'required' => ['_id']],
        'incr' => ['required' => ['_id']],
        'incrby' => ['required' => ['_id', 'value']],
        'incrbyfloat' => ['required' => ['_id', 'value']],
        'keys' => ['getter' => true, 'required' => [':pattern']],
        'lindex' => ['getter' => true, 'required' => ['_id', ':index']],
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
        'sismember' => ['getter' => true, 'required' => ['_id', ':member']],
        'smembers' => ['getter' => true, 'required' => ['_id']],
        'smove' => ['required' => ['_id', 'destination', 'member']],
        'sort' => ['required' => ['_id'], 'opts' => ['alpha', 'by', 'direction', 'get', 'limit']],
        'spop' => ['required' => ['_id'], 'opts' => ['count'], 'mapResults' => 'mapStringToArray'],
        'srandmember' => ['getter' => true, 'required' => ['_id'], 'opts' => ['count'], 'mapResults' => 'mapStringToArray'],
        'srem' => ['required' => ['_id', 'members']],
        'sscan' => ['getter' => true, 'required' => ['_id', 'cursor'], 'opts' => ['match', 'count']],
        'strlen' => ['getter' => true, 'required' => ['_id']],
        'sunion' => ['getter' => true, 'required' => ['keys']],
        'sunionstore' => ['required' => ['destination', 'keys']],
        'time' => ['getter' => true, 'mapResults' => 'mapArrayStringToArrayInt'],
        'touch' => ['required' => ['keys']],
        'ttl' => ['getter' => 'true', 'required' => ['_id']],
        'type' => ['getter' => 'true', 'required' => ['_id']],
        'zadd' => ['required' => ['_id', 'elements'], 'opts' => ['nx', 'xx', 'ch', 'incr']],
        'zcard' => ['getter' => true, 'required' => ['_id']],
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
        'zrank' => ['getter' => true, 'required' => ['_id', ':member']],
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
        'zrevrangebylex' => ['getter' => true, 'required' => ['_id', 'min', 'max'], 'opts' => ['limit']],
        'zrevrangebyscore' => [
            'getter' => true,
            'required' => ['_id', 'min', 'max'],
            'opts' => 'assignZrangeOptions',
            'mapResults' => 'mapZrangeResults'
        ],
        'zrevrank' => ['getter' => true, 'required' => ['_id', ':member']],
        'zscan' => ['getter' => true, 'required' => ['_id', 'cursor'], 'opts' => ['match', 'count']],
        'zscore' => ['getter' => true, 'required' => ['_id', ':member']],
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
        $getter = true;
        $options = ['httpParams' => [], 'query_parameters' => []];

        if (!isset($this->COMMANDS[$command]['getter']) || $this->COMMANDS[$command]['getter'] === false) {
            $getter = false;
        }

        if (isset($this->COMMANDS[$command]['required'])) {
            foreach ($this->COMMANDS[$command]['required'] as $key) {
                $value = array_shift($arguments);

                if ($value === null) {
                    throw new InvalidArgumentException('MemoryStorage.' . $command . ': Missing parameter "' . $key . '"');
                }

                $this->assignParameter($data, $options, $getter, $key, $value);
            }
        }

        $argsLeft = count($arguments);

        if ($argsLeft > 1) {
            throw new InvalidArgumentException('MemoryStorage.' . $command . ': Too many parameters provided');
        }

        if ($argsLeft === 1 && !is_array($arguments[0])) {
            throw new InvalidArgumentException('MemoryStorage.' . $command . ': Invalid optional parameter (expected an associative array)');
        }

        if ($argsLeft == 1) {
            $options = array_merge($options, array_shift($arguments));

            if (isset($this->COMMANDS[$command]['opts'])) {
                if (is_array($this->COMMANDS[$command]['opts'])) {
                    foreach ($this->COMMANDS[$command]['opts'] as $opt) {
                        if (isset($options[$opt])) {
                            $this->assignParameter($data, $options, $getter, $opt, $options[$opt]);
                            unset($options[$opt]);
                        }
                    }
                }
            }
        }

        /*
           Options function mapper does not necessarily should be called
           whether options have been passed by the client or not
         */
        if (isset($this->COMMANDS[$command]['opts']) && is_callable([$this, $this->COMMANDS[$command]['opts']])) {
            $this->{$this->COMMANDS[$command]['opts']}($options);
        }

        $result = $this->kuzzle->query($query, $data, $options);

        if (isset($result['error'])) {
            return $result;
        }

        if (!isset($this->COMMANDS[$command]['mapResults'])) {
            return $result['result'];
        }

        return call_user_func([$this, $this->COMMANDS[$command]['mapResults']], $result['result']);
    }

    /**
     * Assigns the $key => $value argument to the right
     * place in the $data structure
     *
     * Mutates $data and $options
     *
     * @param $data
     * @param $options
     * @param $getter
     * @param $key
     * @param $value
     */
    private function assignParameter(&$data, &$options, $getter, $key, $value)
    {
        if ($key == '_id') {
            $data[$key] = $value;
        } else if ($getter) {
            $converted = is_array($value) ? implode(',', $value) : $value;

            if ($key[0] === ':') {
                $options['httpParams'][$key] = $converted;
            } else {
                $options['query_parameters'][$key] = $converted;
            }
        } else {
            if (!isset($data['body'])) {
                $data['body'] = [];
            }

            $data['body'][$key] = $value;
        }
    }

    /**
     * Assign the provided options for the georadius* redis functions
     * to the request object, as expected by Kuzzle API
     *
     * Mutates the provided options object
     *
     * @param $options
     */
    private function assignGeoRadiusOptions(&$options)
    {
        $parsed = '';

        foreach ($options as $key => $opt) {
            if ($key === 'withcoord' || $key === 'withdist') {
                if (!empty($parsed)) {
                    $parsed .= ',';
                }

                $parsed .= $key;
                unset($options[$opt]);
            } else if ($key === 'count' || $key === 'sort') {
                if (!empty($parsed)) {
                    $parsed .= ',';
                }

                if ($key === 'count') {
                    $parsed .= 'count,';
                }

                $parsed .= $opt;
                unset($options[$opt]);
            }
        }

        if (!empty($parsed)) {
            $options['query_parameters']['options'] = $parsed;
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
    private function assignZrangeOptions(&$options)
    {
        $options['query_parameters']['options'] = 'withscores';

        if (isset($options['limit'])) {
            $options['query_parameters']['limit'] = implode(',', $options['limit']);
            unset($options['limit']);
        }
    }

    /**
     * Maps geopos results, from array<array<string>> to array<array<number>>
     *
     * @param results
     * @return array
     */
    private function mapGeoposResults($results)
    {
        $ret = [];

        foreach ($results as $coords) {
            $point = [];

            foreach ($coords as $latlon) {
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
    private function mapGeoRadiusResults($results)
    {
        $ret = [];

        // Simple array of point names (no options provided)
        if (!is_array($results[0])) {
            foreach ($results as $point) {
                array_push($ret, ['name' => $point]);
            }

            return $ret;
        }

        foreach ($results as $point) {
            // The point id is always the first item
            $p = ['name' => $point[0]];
            $pointLength = count($point);

            for ($i = 1; $i < $pointLength; $i++) {
                // withcoord results are stored in an array...
                if (is_array($point[$i])) {
                    $p['coordinates'] = [];

                    foreach ($point[$i] as $coord) {
                        array_push($p['coordinates'], floatval($coord));
                    }
                } else {
                    // ... while withdist ones are not
                    $p['distance'] = floatval($point[$i]);
                }
            }

            array_push($ret, $p);
        }

        return $ret;
    }

    /**
     * Map a string result to an array of strings.
     * Used to uniformize polymorphic results from redis
     *
     * @param results
     * @return array
     */
    private function mapStringToArray($results)
    {
        return is_array($results) ? $results : array($results);
    }

    /**
     * Map an array of strings to an array of integers
     *
     * @param results
     * @return array
     */
    private function mapArrayStringToArrayInt($results)
    {
        $ret = [];

        foreach ($results as $value) {
            array_push($ret, intval($value));
        }

        return $ret;
    }

    /**
     * Map zrange results with WITHSCORES:
     * [
     *  "member1",
     *  "score of member1",
     *  "member2",
     *  "score of member2"
     * ]
     *
     * into the following format:
     * [
     *  {"member": "member1", "score": <score of member1>},
     *  {"member": "member2", "score": <score of member2>},
     * ]
     *
     *
     * @param results
     * @return array
     */
    private function mapZrangeResults($results)
    {
        $buffer = null;
        $mapped = [];

        foreach ($results as $value) {
            if ($buffer === null) {
                $buffer = $value;
            } else {
                array_push($mapped, ['member' => $buffer, 'score' => floatval($value)]);
                $buffer = null;
            }
        }

        return $mapped;
    }

    /**
     * Allow call_user_func to work as it is always used with this class
     * as a context
     *
     * @param $results
     * @return float
     */
    private function mapStringToFloat($results)
    {
        return floatval($results);
    }
}
