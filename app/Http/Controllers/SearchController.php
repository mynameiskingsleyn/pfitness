<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Helpers\ETLHelper;

use App\Models\Zipcode;

class SearchController extends Controller
{
    //
    public function __construct()
    {
        $this->zipcode = new Zipcode();
    }

    public function index()
    {
        //dd('working');
        $filename = 'us-zip-code-test.csv';
        $zipcodeEtl = new ETLHelper($filename,$this->zipcode);
        $zipcodeEtl->setFilePath();
        $zipcodeEtl->setHeaders();
//        dd($zipcodeEtl->modelHeaders);
//        dd($zipcodeEtl->filePath);


    }


    public function loadZipCodes()
    {
        //$filename = 'us-zip-code-test.csv';
        $filename = 'us-zip-code-latitude-and-longitude.csv';
        $zipcodeEtl = new ETLHelper($filename,$this->zipcode);
        $zipcodeEtl->setHeaders();
        //dd('working');
        $insertValues = $zipcodeEtl->readCSV();
        //dd($zipcodeEtl->modelHeaders);
        dd($insertValues);


    }
}
