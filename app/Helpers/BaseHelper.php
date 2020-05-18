<?php

namespace App\Helpers;

use App\Traits\CacheTrait;

use App\Models\Zipcode;

class BaseHelper
{

    use CacheTrait;
    protected $cachePreFix;
    protected $executeSetting;


    public function __construct()
    {
        $this->cachePreFix = config('trainers.cache_prefix');
        $this->zipModel = new Zipcode();
        $this->executeSetting = true;
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

    function executeSettings()
    {
        if($this->executeSetting == true){
            ini_set('max_execution_time', 0);
            set_time_limit(0);
            ini_set('memory_limit', -1);
        }
    }


}