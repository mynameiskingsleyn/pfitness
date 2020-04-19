<?php

namespace App\Helpers;

use App\Traits\CacheTrait;

use App\Models\Zipcode;

class BaseHelper
{

    use CacheTrait;
    protected $cachePreFix;


    public function __construct()
    {
        $this->cachePreFix = config('trainers.cache_prefix');
        $this->zipModel = new Zipcode();
    }


    public function getCacheDBKey()
    {
        return $this->cachePreFix['cacheDB'];
    }


    public function addToCacheDB($field,$value)
    {
        $cacheDBKey = $this->getCacheDBKey();
        $cachedVals = $this->cacheHMGet($cacheDBKey,'zipcode');


    }


}