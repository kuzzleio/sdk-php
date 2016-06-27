<?php

namespace Kuzzle;

use Exception;

/**
 * Class MemoryStorage
 * @package kuzzleio/kuzzle-sdk
 *
 * @method append
 * @method bgrewriteaof
 * @method bgsave
 * @method bitcount
 * @method bitop
 * @method bitpos
 * @method blpop
 * @method brpoplpush
 * @method dbsize
 * @method decrby
 * @method del
 * @method discard
 * @method exec
 * @method exists
 * @method expire
 * @method expireat
 * @method flushdb
 * @method getbit
 * @method getrange
 * @method hdel
 * @method hexists
 * @method hincrby
 * @method hmset
 * @method hset
 * @method info
 * @method keys
 * @method lastsave
 * @method lindex
 * @method linsert
 * @method lpush
 * @method lrange
 * @method lrem
 * @method lset
 * @method ltrim
 * @method mset
 * @method multi
 * @method object
 * @method pexpire
 * @method pexpireat
 * @method pfadd
 * @method pfmerge
 * @method ping
 * @method psetex
 * @method publish
 * @method randomkey
 * @method rename
 * @method renamenx
 * @method restore
 * @method rpoplpush
 * @method sadd
 * @method save
 * @method set
 * @method sdiffstore
 * @method setbit
 * @method setex
 * @method setrange
 * @method sinterstore
 * @method sismember
 * @method smove
 * @method sort
 * @method spop
 * @method srem
 * @method sunionstore
 * @method unwatch
 * @method wait
 * @method zadd
 * @method zcount
 * @method zincrby
 * @method zinterstore
 * @method zlexcount
 * @method zrange
 * @method zrangebylex
 * @method zrangebyscore
 * @method zrem
 * @method zremrangebylex
 * @method zremrangebyscore
 * @method zrevrangebylex
 * @method zrevrangebyscore
 * @method zrevrank
 * @method zcard
 * @method type
 * @method ttl
 * @method strlen
 * @method smembers
 * @method scard
 * @method rpop
 * @method pttl
 * @method persist
 * @method lpop
 * @method llen
 * @method incr
 * @method hvals
 * @method hstrlen
 * @method hlen
 * @method hkeys
 * @method hgetall
 * @method dump
 * @method get
 * @method decr
 * @method lpushx
 * @method getset
 * @method watch
 * @method sunion
 * @method sinter
 * @method sdiff
 * @method pfcount
 * @method mget
 * @method incrbyfloat
 * @method incrby
 * @method brpop
 * @method hget
 * @method hmget
 * @method hsetnx
 * @method msetnx
 * @method rpush
 * @method hincrbyfloat
 * @method srandmember
 * @method zrevrange
 * @method zscore
 */
class MemoryStorage
{
    private $COMMANDS = [
        "append" => ["id", "value"],
        "bgrewriteaof" => [],
        "bgsave" => [],
        "bitcount" => ["id", "start", "end"],
        "bitop" => ["operation", "destkey", ["id", "keys"]],
        "bitpos" => ["id", "bit", ["__opts__" => ["start", "end"]]],
        "blpop" => [["id", "keys"], "timeout"],
        "brpoplpush" => ["source", "destination"],
        "dbsize" => [],
        "decrby" => ["id", "value"],
        "del" => [["id", "keys"]],
        "discard" => [],
        "exec" => [],
        "exists" => [["id", "keys"]],
        "expire" => ["id", "seconds"],
        "expireat" => ["id", "timestamp"],
        "flushdb" => [],
        "getbit" => ["id", "offset"],
        "getrange" => ["id", "start", "end"],
        "hdel" => ["id", ["field", "fields"]],
        "hexists" => ["id", "field"],
        "hincrby" => ["id", "field", "value"],
        "hmset" => ["id", "values"],
        "hset" => ["id", "field", "value"],
        "info" => ["section"],
        "keys" => ["pattern"],
        "lastsave" => [],
        "lindex" => ["id", "idx"],
        "linsert" => ["id", "position", "pivot", "value"],
        "lpush" => ["id", ["value", "values"]],
        "lrange" => ["id", "start", "stop"],
        "lrem" => ["id", "count", "value"],
        "lset" => ["id", "idx", "value"],
        "ltrim" => ["id", "start", "stop"],
        "mset" => ["values"],
        "multi" => [],
        "object" => ["subcommand", "args"],
        "pexpire" => ["id", "milliseconds"],
        "pexpireat" => ["id", "timestamp"],
        "pfadd" => ["id", ["element", "elements"]],
        "pfmerge" => ["destkey", ["sourcekey", "sourcekeys"]],
        "ping" => [],
        "psetex" => ["id", "milliseconds", "value"],
        "publish" => ["channel", "message"],
        "randomkey" => [],
        "rename" => ["id", "newkey"],
        "renamenx" => ["id", "newkey"],
        "restore" => ["id", "ttl", "content"],
        "rpoplpush" => ["source", "destination"],
        "sadd" => ["id", ["member", "members"]],
        "save" => [],
        "set" => ["id", "value", ["__opts__" => ["ex", "px", "nx", "xx"]]],
        "sdiffstore" => ["destination", ["id", "keys"]],
        "setbit" => ["id", "offset", "value"],
        "setex" => ["id", "seconds", "value"],
        "setrange" => ["id", "offset", "value"],
        "sinterstore" => ["destination", ["id", "keys"]],
        "sismember" => ["id", "member"],
        "smove" => ["id", "destination", "member"],
        "sort" => ["id", ["__opts__" => ["by", "offset", "count", "get", "direction", "alpha", "store"]]],
        "spop" => ["id", "count"],
        "srem" => ["id", ["member", "members"]],
        "sunionstore" => ["destination", ["id", "keys"]],
        "unwatch" => [],
        "wait" => ["numslaves", "timeout"],
        "zadd" => ["id", ["__opts__" => ["nx", "xx", "ch", "incr", "score", "member", "members"]]],
        "zcount" => ["id", "min", "max"],
        "zincrby" => ["id", "value", "member"],
        "zinterstore" => ["destination", ["id", "keys"], ["__opts__" => ["weight", "weights", "aggregate"]]],
        "zlexcount" => ["id", "min", "max"],
        "zrange" => ["id", "start", "stop", ["__opts__" => ["withscores"]]],
        "zrangebylex" => ["id", "min", "max", ["__opts__" => ["offset", "count"]]],
        "zrangebyscore" => ["id", "min", "max", ["__opts__" => ["withscores", "offset", "count"]]],
        "zrem" => ["id", "member"],
        "zremrangebylex" => ["id", "min", "max"],
        "zremrangebyscore" => ["id", "min", "max"],
        "zrevrangebylex" => ["id", "max", "min", ["__opts__" => ["offset", "count"]]],
        "zrevrangebyscore" => ["id", "max", "min", ["__opts__" => ["withscores", "offset", "count"]]],
        "zrevrank" => ["id", "member"],
        "zcard" => ["id"],
        "type" => ["id"],
        "ttl" => ["id"],
        "strlen" => ["id"],
        "smembers" => ["id"],
        "scard" => ["id"],
        "rpop" => ["id"],
        "pttl" => ["id"],
        "persist" => ["id"],
        "lpop" => ["id"],
        "llen" => ["id"],
        "incr" => ["id"],
        "hvals" => ["id"],
        "hstrlen" => ["id"],
        "hlen" => ["id"],
        "hkeys" => ["id"],
        "hgetall" => ["id"],
        "dump" => ["id"],
        "get" => ["id"],
        "decr" => ["id"],
        "lpushx" => ["id", "value"],
        "getset" => ["id", "value"],
        "watch" => [["id", "keys"]],
        "sunion" => [["id", "keys"]],
        "sinter" => [["id", "keys"]],
        "sdiff" => [["id", "keys"]],
        "pfcount" => [["id", "keys"]],
        "mget" => [["id", "keys"]],
        "incrbyfloat" => ["id", "value"],
        "incrby" => ["id", "value"],
        "brpop" => [["id", "keys"], "timeout"],
        "hget" => ["id", "field"],
        "hmget" => ["id", ["field", "fields"]],
        "hsetnx" => ["id", "field", "value"],
        "msetnx" => ["values"],
        "rpush" => ["id", ["value", "values"]],
        "hincrbyfloat" => ["id", "field", "value"],
        "srandmember" => ["id", "count"],
        "zrevrange" => ["id", "start", "stop", ["__opts__" => ["withscores"]]],
        "zscore" => ["id", "member"]
    ];
    protected $kuzzle;

    public function __construct(Kuzzle $kuzzle)
    {
        $this->kuzzle = $kuzzle;
    }

    public function __call($command, $arguments)
    {
        if (!array_key_exists($command, $this->COMMANDS)) {
            throw new Exception('MemoryStorage: Command "' . $command . '" not found');
        }

        $data = [];
        $query = [
            'controller' => 'memoryStorage',
            'action' => $command
        ];

        foreach ($this->COMMANDS[$command] as $key => $value) {
            if (!array_key_exists($key, $arguments)) {
                continue;
            }

            if (is_array($value)) {
                $value = is_array($arguments[$key]) ? $value[1] : $value[0];
            }

            if ($value === 'id') {
                $data['_id'] = $arguments[$key];
            } else {
                if (!array_key_exists('body', $data)) {
                    $data['body'] = [];
                }

                if (array_key_exists('__opts__', $value)) {
                    foreach ($value['__opts__'] as $arg) {
                        if (array_key_exists($arg, $arguments[$key])) {
                            $data['body'][$arg] = $arguments[$key][$arg];
                        }
                    }
                } else {
                    $data['body'][$value] = $arguments[$key];
                }
            }
        }

        return $this->kuzzle->query($query, $data);
    }

}