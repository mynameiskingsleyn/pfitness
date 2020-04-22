<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Helpers\ETLHelper;

use App\Helpers\UserHelper;

use App\Models\Zipcode;

use App\Helpers\ZipCodeHelper;

class SearchController extends Controller
{
    //
    public function __construct(UserHelper $userHelp, Zipcode $zipcode, ZipCodeHelper $zipCodeHelper)
    {
        //$this->zipcode = new Zipcode();
        $this->userHelper = $userHelp;
        $this->zipcodeHelper = $zipCodeHelper;
    }

    public function index()
    {
        $miles = 100;
        $zipCode = $this->zipcodeHelper->getCurrentZip();
        $users = $this->userHelper->getUsersInZip($zipCode,$miles);
    }

    /**  loads zipcode db */
    public function loadZipCodes()
    {
        //$filename = 'us-zip-code-test.csv';
        $filename = 'us-zip-code-latitude-and-longitude.csv';
        $zipcodeEtl = new ETLHelper($filename,$this->zipcode);
        $zipcodeEtl->setHeaders();
        //dd('working');
        \Log::info('<-------------Loading zip codes---------------->');
        try{
            $insertValues = $zipcodeEtl->readCSV();
        }catch(\Exception $e){
            return "Error loading zipcodes.... ".$e->getMessage();
        }

        //dd($zipcodeEtl->modelHeaders);
        \Log::info("zipcodes load completed !!!");
        if($insertValues){
            return "Zip code loading completed ............";
        }else{
            return "Zip code was unsuccessful ............";
        }

        //dd($insertValues);


    }
}
