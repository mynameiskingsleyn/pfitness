<?php

namespace App\Helpers;

use App\Traits\CacheTrait;
use App\Models\Zipcode;

use Illuminate\Support\Facades\Session;


class ZipCodeHelper extends BaseHelper
{
    use CacheTrait;

    public function __construct()
    {
        parent::__construct();
        //dd($this->cachePreFix);
        $this->zipPrefix = $this->cachePreFix['zipcode'];
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

        $result= null;
        $zipsNearJson = $this->cacheGet($zipCache);
       // dd($zipsNearJson);
        if(empty($zipsNearJson)){
            $zipsNear = $this->zipcodeModel->getZipCodes($this->zipCode,$this->radius);
            //dd($zipsNear);
            if(!empty($zipsNear)){
                $this->cacheSet($zipCache,json_encode($zipsNear));
            }
        }
        $zipsNearJson = $this->cacheGet($zipCache);
        if($zipsNearJson){
            $result = json_decode($zipsNearJson,true);
        }

        return $result;
    }

    public function getZipNearCacheKey($zipcode,$radius)
    {
        $this->radius = $radius;
        $this->zipCode = $zipcode;
        return $cacheName = $this->zipPrefix.$zipcode.$radius;
    }

    public function getCurrentZip()
    {
        if(Session::has('current_zip')){
            return Session::get('current_zip');
        }else{
            $this->setCurrentZip('48084');
        }
        return '48084';
    }

    public function setCurrentZip($zipcode)
    {
        Session::put('current_zip',$zipcode);
    }

    public function getZipsNear($zipcode,$miles)
    {
        $cachename = $this->getZipNearCacheKey($zipcode,$miles);
        $value = $this->getCachedZipCodesNearZipcode($cachename);
       // dd($value);
        return $value;
    }

    public function loadZipsSearch($zip)
    {
       return $this->zipcodeModel->where('zip','LIKE',"$zip%")->pluck('zip');
    }


}