<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
//use App\Helpers\ZipCodeHelper;

use App\Models\User;

class Zipcode extends Model
{
    //
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

    public function setCurrentZip($zipCode)
    {
        $this->currentZip = $zipCode;
    }


    public function getZipInfo($zipCode)
    {
        return $this->where(['zip'=>$zipCode])->first();

    }

    public function getZipCodes($zipCode,$radius)
    {

        //dd($zipCode);
        $currentZip = $this->getZipInfo($zipCode);
        //dd($currentZip);
        $lat = $currentZip->latitude;
        $lon = $currentZip->longitude; //dd($lon);
        $zipCodes = \DB::select("SELECT distinct(zc.zip), SQRT(POWER(69.1 * (zc.latitude - ".$lat."), 2) + POWER(69.1 * (".$lon." - zc.longitude) * COS(zc.latitude / 57.3), 2)) AS `distance_in_miles`
            FROM `".$this->table."` AS zc
            HAVING (`distance_in_miles` <= ".$radius." AND  `distance_in_miles` > 0)
            ORDER BY `distance_in_miles` ASC;");

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

        \Log::debug('comparing '.$first.' and '.$second);
        $firstInfo = $this->where('zip',$first)->first();
        $secondInfo = $this->where('zip',$second)->first();
        $firstLat = $firstInfo->latitude; $firstLon = $firstInfo->longitude;
        $secondLat = $secondInfo->latitude; $secondLon = $secondInfo->longitude;

        $distance = sqrt(pow(69.1*($firstLat - $secondLat),2) + Pow(69.1 * ($secondLon - $firstLon) * cos($firstLat / 57.3), 2));
        \Log::debug("distance between two is $distance");
        return $distance;
    }

}
