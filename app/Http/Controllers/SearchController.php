<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Helpers\ETLHelper;

use App\Helpers\UserHelper;





class SearchController extends MainController
{
    //
    public function __construct(UserHelper $userHelp)
    {
        $this->userHelper = $userHelp;

    }

    public function index(Request $request)
    {
//        $miles = $request->get('miles') ?? 2;
//        $zip = $request->get('zip') ?? '48341';
//        $users = $this->userHelper->getUsersInZip($zip,$miles);


        //dd($pagenate);
        $url = $this->pagenationCheck($request);
        if($url){
            //return redirect($url);
        }
        //dd('continue after redirection');
        //
        $result = $this->userHelper->getUsers($request);
        $users = $result['users'];
        $zip = $result['zip'];
        $miles =$result['miles'];
        $nextPage = isset($result['next_page']) ? $result['next_page'] :'';
        $prevPage = isset($result['prev_page']) ? $result['prev_page'] :'';
        $pageGroup = isset($result['page_group']) ? $result['page_group'] :'';
        $currentPage = isset($result['current_page']) ? $result['current_page'] :'';
        $count = isset($result['count']) ? $result['count'] :'';
        //dd('herer now');
        //$oneUser = $users->first();
        //if($pageGroup){
            //$pageGroup = json_encode($pageGroup);
            $pageGroup = collect($pageGroup);
        //}
        //dd($nextPage);
       // dd($result);
        //dd($pageGroup);
        return view('search.search',compact('users','zip','miles','nextPage','prevPage','count','pageGroup','currentPage'));
        //return view('search.search',compact('users','zip','miles','nextPage','prevPage','count','pageGroup','currentPage'));
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
