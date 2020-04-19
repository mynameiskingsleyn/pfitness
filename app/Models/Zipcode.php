<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
//use App\Helpers\ZipCodeHelper;

use User;

class Zipcode extends Model
{
    //
    public $currentZip;

    protected $table ='zipcodes';

    public function __construct()
    {
        $this->users = new User();
        //$this->helper = new ZipCodeHelper();
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
        return $this->where(['zipcode'=>$zipCode])->first();

    }

    public function getZipCodes($zipCode,$radius)
    {
        $currentZip = $this->getZipInfo($zipCode);
        $lat = $currentZip->latitude;
        $lon = $currentZip->longitude;
        $zipCodes = DB::select("SELECT distinct(zc.zipcode), SQRT(POWER(69.1 * (zc.latitude - ".$lat."), 2) + POWER(69.1 * (".$lon." - zc.longitude) * COS(zc.latitude / 57.3), 2)) AS `distance_in_miles`
            FROM `".$this->table."` AS zc, 
            HAVING (`distance_in_miles` <= ".$radius." AND  `distance_in_miles` > 0)
            ORDER BY `distance_in_miles` ASC;");
        return $zipCodes;
    }

    public function CacheZipCodes($zipCode,$value)
    {
        $result = $this->helper->cacheHMGet()
    }
}
