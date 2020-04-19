<?php

namespace App\Helpers;

use App\Traits\CacheTrait;
use App\Models\Zipcode;


class ZipCodeHelper extends BaseHelper
{
    use CacheTrait;

    public function __construct()
    {
        //parent::__construct();
        $this->cachePrefix = parent::cachePrefix['zipcode'];
        $this->zipcodeModel = new Zipcode();
        $this->zipCode = '';
    }

    public function zipValidation($zipcode)
    {
        $cacheKey = $this->getZipCacheKey($zipcode);

        if($this->isCacheExists($cacheKey)){
            return true;
        }else{
            $getZip = $this->zipModel->where()->first();
        }
    }

    public function cacheNearZips($cacheName, $value)
    {
        //$cacheName = $this->getZipCacheKey($zipcode,$radius);//$this->cacheprefix.$zipcode.$radius;
        $saved = $this->cacheSet($cacheName,$value);
        return $saved;
    }

    public function getCachedZipCodesNearZipcode($zipCache)
    {

        $zipsNear = $this->cacheGet($zipCache);
        if(empty($zipsNear)){
            $zipsNear = $this->zipcodeModel->getZipCodes($this->zipCode,$this->radius);
            if(!empty($zipsNear)){
                $this->cacheNearZips($zipCache,$zipsNear);
            }
        }
        return $zipsNear;
    }

    public function getZipNearCacheKey($zipcode,$radius)
    {
        $this->radius = $radius;
        $this->zipCode = $zipcode;
        return $cacheName = $this->cacheprefix.$zipcode.$radius;
    }
}