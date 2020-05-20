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
        $url = $this->pagenationCheck($request);
        if($url){
            return redirect($url);
        }
        //dd('here yall');
        $result = $this->userHelper->getUsers($request);

        $users = $result['users'];
        $zip = $result['zip'];
        $miles =$result['miles'];
        $next_page = isset($result['next_page']) ? $result['next_page'] :'';
        $prev_page = isset($result['prev_page']) ? $result['prev_page'] :'';
        $page_group = isset($result['page_group']) ? $result['page_group'] :'';
        $current_page = isset($result['current_page']) ? $result['current_page'] :'';
        $count = isset($result['count']) ? $result['count'] :'';
        //$users =  $allusers->toArray();
        return response(['users'=>$users,'zip'=>$zip,'miles'=>$miles,'nextpage'=>$next_page,
        'prevpage'=>$prev_page, 'pagegroup'=>$page_group,'currentpage'=>$current_page,'count'=>$count],

            200);
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
