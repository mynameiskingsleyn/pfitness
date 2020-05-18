<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Http\Controllers\MainController;

use App\Helpers\ZipCodeHelper;

use Illuminate\Http\Request;

use App\Helpers\UserHelper;

class SearchController extends MainController
{
    //
    public function __construct(UserHelper $userHelp)
    {
        $this->zipHelper = new ZipCodeHelper();
        $this->userHelper = $userHelp;
    }

    public function index(Request $request)
    {
        $result = $this->userHelper->getUsers($request);
        $users = $result['users'];
        $zip = $result['zip'];
        $miles =$result['miles'];
        //$users =  $allusers->toArray();
        return response(['users'=>$users,'zip'=>$zip,'miles'=>$miles],200);
    }

    public function zipCodeAutoComplete(Request $request)
    {
        $zip = $request->get('zipPart');
        //dd($request->all());
        if($zip){
            $zips = $this->zipHelper->loadZipsSearch($zip);
            return response(['zips'=>$zips], 200);
        }

        return response(['zips'=>[]],200);
    }
}
