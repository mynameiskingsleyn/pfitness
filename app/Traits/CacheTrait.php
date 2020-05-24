<?php

namespace App\Traits;

use Illuminate\Support\Facades\Redis;
//use Illuminate\Database\Eloquent\Model;

trait CacheTrait
{
    private $array, $tmp_key_chunk;

    public function CacheFlushAll()
    {
        Redis::flushall();

    }

    public function cacheDelete($key)
    {
        return Redis::del($key);
    }

    public function isCacheExists($rKey)
    {
        return Redis::exists($rKey);
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

        \Log::debug(Redis::keys('my_business_database_*'));
        $this->tmp_key_chunk = $key;
        Redis::pipeline(function($pipe){
            \Log::debug("bulk clearing $this->tmp_key_chunk");
           foreach(Redis::keys($this->tmp_key_chunk.'*') as $key){
               \Log::info("deleting cache name $key");
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