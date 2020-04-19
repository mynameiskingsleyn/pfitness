<?php

namespace App\Traits;

use Illuminate\Support\Facades\Redis;
use Illuminate\Database\Eloquent\Model;

trait CacheTrait
{
    private $array, $tmp_key_chunk;

    public function CacheFlushAll()
    {
        Redis::flushall();

    }

    public function isCacheExists($rKey)
    {
        return Redis::exist($rKey);
    }

    public function cacheSet($key, $value)
    {
        Redis::set($key,$value);
        return $this->isCacheExists($key);
    }

    public function cacheGet($key)
    {
        return Redis::get($key);
    }

    public function bulkDelete($key)
    {
        $this->tmp_key_chunk = $key;
        Redis::pipeline(function($pipe){
           foreach(Redis::keys($this->tmp_key_chunk.'*') as $key){
               $pipe->del($key);
           }
        });
    }

    /*************** HM KEYS ****************/

    public function hexists($key, $field)
    {
        return Redis::hexists($key,$field);
    }

    public function cacheHMSet($key, $field, $value)
    {
        Redis::hmset($key,$field,$value);
        return $this->hexists($key,$field);
    }

    public function cacheHMGet($key,$field)
    {
        return Redis::hmget($key,$field);
    }

    public function cacheHMGetAll($key)
    {
        return Redis::hgetall($key);
    }

    public function cacheHMGetFields($key)
    {
        return Redis::hkeys($key);
    }

    public function cacheHMGetValues($key)
    {
        return Redis::hvals($key);
    }
}