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

    public function index(Request $request)
    {
        $miles = 4;
        $zip = '48341';
        if($zip){
            $this->zipcodeHelper->setCurrentZip($zip);
        }
        $zipCode = $this->zipcodeHelper->getCurrentZip();
        $users = $this->userHelper->getUsersInZip($zipCode,$miles);
        foreach($users as $aUser){
            //dd($aUser);
            $aUser->setDistanceFromSelectedZipAttribute();
        }
//        $users = $users->sortBy(function($user){
//            return $user->distance;
//        });
        $users=$users->sortBy(['lname']);
        $users=$users->sortBy(['distance']);
        // sort

        $oneUser = $users->first();
        //dd($oneUser->distance);
        //dd($oneUser);
        //dd($oneUser->DistanceFromZip());
        //dd('here');
        //dd($users->sortBy('distanceFromSelectedZip'));
        dd($users);
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
