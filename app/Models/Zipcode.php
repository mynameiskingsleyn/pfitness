<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\BaseModel;
//use App\Helpers\ZipCodeHelper;

use App\Models\User;

use App\Traits\CacheTrait;

class Zipcode extends BaseModel
{
    //
    use CacheTrait;
    public $currentZip;

    protected $table ='zipcodes';

    public function __construct()
    {

        $this->users = new User();
        //$this->helper = new ZipCodeHelper();
        $this->etlFields = ['zip'=>100,'city'=>100,'state'=>100,'latitude'=>100,'longitude'=>100];
    }


    public function getUsersWithIn($zipCode, $raduis)
    {
        $zipInfo = $this->getZipInfo($zipCode);
        if($zipInfo){

        }

    }

    public function Users()
    {
        return $this->hasMany(User::class,'zipcode','zip');
    }

    public function setCurrentZip($zipCode)
    {
        $this->currentZip = $zipCode;
    }


    public function getZipInfo($zipCode)
    {
        return $this->where(['zip'=>$zipCode])->first();

    }

    public function getZipCodesWithUsers_copy($zipCode,$radius)
    {
        $zipCodes=null;
        $currentZip = $this->getZipInfo($zipCode);
        //dd($currentZip);
        if($currentZip) {
            $lat = $currentZip->latitude;
            $lon = $currentZip->longitude; //dd($lon);
            $users = \DB::select("SELECT distinct(zc.zip), us.id,us.fname,us.lname,us.email,us.zipcode,
             us.city,us.state,us.country,
            SQRT(POWER(69.1 * (zc.latitude - ".$lat."), 2) + POWER(69.1 * (".$lon." - zc.longitude) * COS(zc.latitude / 57.3), 2)) AS `distance`
            FROM `".$this->table."` AS zc
            LEFT JOIN `users` AS us on us.zipcode = zc.zip
            HAVING (`distance` <= ".$radius." AND  `distance` >= 0)
            ORDER BY `distance` ASC;");
        }
        return $users;
    }

    public function getZipCodesWithUsers($zipCode,$radius)
    {
        $zipCodes=null;
        $currentZip = $this->getZipInfo($zipCode);
        //dd($currentZip);
        if($currentZip) {
            $lat = $currentZip->latitude;
            $lon = $currentZip->longitude; //dd($lon);



            $zips = Zipcode::SelectRaw("Select distinct(zipcodes.zip),
            SQRT(POWER(69.1 * (zipcodes.latitude - ".$lat."), 2) + POWER(69.1 * (".$lon." - zipcodes.longitude) * COS(zipcodes.latitude / 57.3), 2)) AS `distance`
            HAVING (`distance` <= ".$radius." AND  `distance` >= 0)
            ORDER BY `distance` ASC;")->get();

//            $users = \DB::select("SELECT distinct(zc.zip), us.id,us.fname,us.lname,us.email,us.zipcode,
//             us.city,us.state,us.country,
//            SQRT(POWER(69.1 * (zc.latitude - ".$lat."), 2) + POWER(69.1 * (".$lon." - zc.longitude) * COS(zc.latitude / 57.3), 2)) AS `distance`
//            FROM `".$this->table."` AS zc
//            LEFT JOIN `users` AS us on us.zipcode = zc.zip
//            HAVING (`distance` <= ".$radius." AND  `distance` >= 0)
//            ORDER BY `distance` ASC;");
        }
        return $zips;
    }

    public function getZipCodes($zipCode,$radius)
    {

        //dd($zipCode);
        $zipCodes=null;
        $currentZip = $this->getZipInfo($zipCode);
        //dd($currentZip);
        if($currentZip){
            $lat = $currentZip->latitude;
            $lon = $currentZip->longitude; //dd($lon);
            $zipCodes = \DB::select("SELECT distinct(zc.zip), SQRT(POWER(69.1 * (zc.latitude - ".$lat."), 2) + POWER(69.1 * (".$lon." - zc.longitude) * COS(zc.latitude / 57.3), 2)) AS `distance_in_miles`
            FROM `".$this->table."` AS zc
            HAVING (`distance_in_miles` <= ".$radius." AND  `distance_in_miles` >= 0)
            ORDER BY `distance_in_miles` ASC;");
        }
        //dd($zipCodes);

        // cache distance for feature use
        if($zipCodes)
            $this->cacheZipDist($zipCode,$zipCodes);

        return $zipCodes;
    }

    public function getEtlFields()
    {
        return $this->etlFields;
    }

    public function updateEtlFields($fieldsArray)
    {
        $this->etlFields = $fieldsArray;
    }

    public function distanceApart($first,$second)
    {
        $cacheName = $this->getZipDistCacheName($first,$second);
        $distance = $this->getDistFromCache($cacheName);
        if(!empty($distance) || $distance == 0){
            return $distance;
        }
        $firstInfo = $this->where('zip',$first)->first();
        $secondInfo = $this->where('zip',$second)->first();
        $firstLat = $firstInfo->latitude; $firstLon = $firstInfo->longitude;
        $secondLat = $secondInfo->latitude; $secondLon = $secondInfo->longitude;
        \Log::debug("$cacheName is not cached and value is $distance");
        $distance = sqrt(pow(69.1*($firstLat - $secondLat),2) + Pow(69.1 * ($secondLon - $firstLon) * cos($firstLat / 57.3), 2));
        $this->addDistToCache($cacheName,$distance);
        return $distance;
    }

    public function getZipDistCacheName($firstZip,$secondZip)
    {
        $sortArray = [$firstZip,$secondZip];
        sort($sortArray);
        //dd($sortArray);
        $sorted = implode(':',$sortArray);
        return 'zip_dist:'.$sorted;
    }

    public function cacheZipDist($currentZip,$coll)
    {
        //$check = [];
        foreach($coll as $aZip){
            $dist = $aZip->distance_in_miles;
            $zipcode = $aZip->zip;
            $cacheName = $this->getZipDistCacheName($currentZip,$zipcode);
            //dd($cacheName);
            $this->addDistToCache($cacheName,$dist);
            //$check[$cacheName] = $this->getDistFromCache($cacheName);
        }
        //dd($check);

    }

    public function addDistToCache($cacheName,$dist)
    {
        if(empty($this->getDistFromCache($cacheName))){
            $this->cacheSet($cacheName,$dist);
        }
    }

    public function getDistFromCache($cacheName)
    {
        return $this->cacheGet($cacheName);
    }

}
