<?php

namespace App\Helpers;

use App\Helpers\ZipCodeHelper;
use App\Models\User;
use App\Traits\CacheTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Nahid\JsonQ\Jsonq;

class UserHelper extends BaseHelper
{
    use CacheTrait;
    public $pagenate;
    public function __construct(User $user, ZipCodeHelper $zipCodeHelper)
    {
        $this->user = $user;
        $this->zipcodeHelper = $zipCodeHelper;
        $this->pagenate = config('trainers.pagination.pagenate');
        $this->perPage = config('trainers.pagination.perpage');
        $this->pageMaxGroup = config('trainers.pagination.maxGroup');
        $this->defaults = [
            'miles'=>config('trainers.defaults.dist'),
            'zipcode'=>config('trainers.default.zip')
        ];
        $this->maxDist = 100;
    }
    public function getUsersInZip($zipcode='48326',$miles=5)
    {

        $startTime = $this->getTime();
        //$this->clearAllCache();
        $distance = $miles ?? 2;
        if($distance > 100){
            $distance = 100;
        }
        if($zipcode){
            $this->zipcodeHelper->setCurrentZip($zipcode);
        }
        $zipcode = $zipcode ?? $this->zipcodeHelper->getCurrentZip();
        if($zipcode){

            $cacheName = $this->getCacheName($zipcode,$distance);
            $usersResult = $this->getCacheItem($cacheName);
            //dd($usersResult);
            if(!$usersResult){
                $zipsWithinCode = $this->zipcodeHelper->getZipsNear($zipcode,$distance);
                if($zipsWithinCode){
                    $zips = array_column($zipsWithinCode,'zip');
                    // check if inventory is cached...
                    $findCacheName = $this->findParentInventory($zipcode,$distance);
                    $keys = array_keys($findCacheName);
                    $dist = $findCacheName[$keys[0]];
                    $inventCacheName = $keys[0];
                    //$inventCacheName = $this->getCacheName($zipcode,$this->maxDist);
                    //dd($inventCacheName);
                    $inventUsers = $this->getRawCacheItem($inventCacheName);
                    if($inventUsers){
                        //use inventory to create specific search
                        $users = $this->getUserSearchWithInventory($inventUsers,$zips);
                        $this->cacheItem($cacheName,$users);

                        //else create inventory save inventory use it to create specific and save specific cache
                    }else{
                        // create inventory and use to create search.

                        $this->createInventory($zipcode,$inventCacheName,$dist);
                        $inventUsers = $this->getRawCacheItem($inventCacheName);
                        $users = $this->getUserSearchWithInventory($inventUsers,$zips);
                        $this->cacheItem($cacheName,$users);
                        //dd($users);

                    }


                }
                $usersResult = $this->getCacheItem($cacheName);
                //dd($usersResult);
            }
            $endTime = $this->getTime();
            $completTime = $this->timeDiff($startTime,$endTime);
            \Log::debug("time taken to run  all user fetch is $completTime ");
            //dd($usersResult);
            return $usersResult;

        }
        //        $users = $users->sortBy(function($user){
//            return $user->distance;
//        });

        return [];

    }

    public function getUserSearchWithInventory($jsonInventory,$zips)
    {
        // convert to string and back to json to add table name;
        $arrayInvent = json_decode($jsonInventory,true);
        $usersG = ['users'=>$arrayInvent];
        $data = json_encode($usersG);
        //dd($data);

        $q = new Jsonq();
        $users = $q->json($data)
                    ->from('users')
                    ->whereIn('zipcode',$zips)
                    ->get();
        return $users;
    }
    public function createInventory($zipcode,$cacheName,$dist)
    {
        //dd($dist);
        //$cacheName = $this->getCacheName($zipcode,$this->maxDist);
        \Log::debug("inventory cache used is $cacheName and distance is $dist");
        $zipsWithinCode = $this->zipcodeHelper->getZipsNear($zipcode,$dist);
        $zips = array_column($zipsWithinCode,'zip');
        //dd($zips);
        $users = $this->user->whereIn('zipcode',$zips)->get();
        //dd($users);
        //dd($users);
        if($users){
            //dd($users);
            //\Log::info('Sorted values below------------------------------->');
            $sortStart = $this->getTime();
            //$result = $users;
            $result=$users->sortBy(function($value) {
                $dist = $value->distance;
                $pref = $value->preferred;
                $int = filter_var($dist, FILTER_SANITIZE_NUMBER_INT);
                $intVal = intval($int);

                if($pref =='true'){
                    $intVal = $intVal - 50;
                    //dd($intVal);
                }

                return intval($intVal);
            });


            $newUsers = [];
            foreach($result as $aUser){
                $newUsers[] = $aUser;
            }
            //dd($newUsers);
            $this->cacheItem($cacheName,$newUsers);
            $sortEnd = $this->getTime();
            $sortComplete = $this->timeDiff($sortStart,$sortEnd);
            //dd('here cook');
            //dd($newUsers);
            \Log::debug("time taken to sort users is $sortComplete");

        }
    }

    /**
     * @param $request
     * @return mixed
     * camputures matches for user in search input
     */
    public function getUsersSearch($request)
    {
        $miles = $request->get('dist') ?? $this->defaults['miles'];

        $zip = $request->get('zip') ?? $this->defaults['zipcode'];
        $users = $this->getUsersInZip($zip,$miles);
        $search = $request->get('search') ?? '';
        //dd($search);
        $search = strtolower($search);

        //dd($users);
        $usersLocate = $this->findMatches($users,['fullname'],'fullname',$search);
        //dd($usersLocate);
        //extract location for creating pages
        $spots = array_keys($usersLocate);
        $names = array_values($usersLocate);
        //generate pages using positons
        $pagenumbers = $this->getUsersPageNumbers($users,$spots);
        $url = $request->fullUrl();
        //dd($url);
        // remove _search from url
        $url = str_replace('_search','',$url);
        //remove search item from url
        $pattern = '/(&search=\S*)(&{1}\S*)/';
        $url = preg_replace($pattern,'$2',$url);
        $pageUrls = [];
        foreach($pagenumbers as $page ){
            $pageUrls[$page] = $this->updatePageNum($url,$page);
        }
        return [
            'items'=>$names,
            'pages'=>$pageUrls
        ];


    }

    public function getUsers(Request $request)
    {
        //dd($this);

        $miles = $request->get('dist') ?? 2;

        $zip = $request->get('zip') ?? '48341';
        $users = $this->getUsersInZip($zip,$miles);
        $next_page = null;
        $prev_page = null;
        $page_group = null;
        $reqParams =[];
        //dd($request->fullUrl());

        if($this->pagenate){
            if(!$request->has('zip')){
                $request->request->add(['zip'=>$zip]);
                $reqParams['zip'] = $zip;
            }
            if(!$request->has('dist')){
                $request->request->add(['dist'=>$miles]);
                $reqParams['dist'] = $miles;
            }
            //$url = $this->rebuildQueryWithPagination($request,[]);
            //dd($url);
            $usersInfo = $this->pagenateItem($request,$users);
            if($usersInfo){
                //dd($usersInfo);
                $users = $usersInfo['users'];
                $next_page = isset($usersInfo['nextPage'])? $usersInfo['nextPage'] :'';
                //dd($next_page);
                $prev_page = isset($usersInfo['prevPage']) ? $usersInfo['prevPage'] :'';
                $page_group = isset($usersInfo['pageGroup']) ? $usersInfo['pageGroup'] : [];
                $current_page=isset($usersInfo['currentPage'])?$usersInfo['currentPage']:'';
                $count = $usersInfo['count'];
            }

        }
        //dd('here yalfffl');
        $finalUsers = collect($users);
        return ['users'=>$finalUsers,'zip'=>$zip,'miles'=>$miles,'next_page'=>$next_page,
            'prev_page'=>$prev_page,'page_group'=>$page_group,  'count'=>$count,'current_page'=>$current_page];
    }

    public function getCacheName($zip,$distance,$jobCode='')
    {
        return $zip.'_users_'.$distance.'_'.$jobCode;
    }






    /// end of cache function s

    // start of pagination task


    public function addPG($url,$pg=1) // pagegroup is identified by pg
    {
        $pattern = '/pg=\d?/';
        $hasPg = preg_match($pattern,$url);
        if($hasPg){
            $newUrl = preg_replace($pattern,'pg='.$pg, $url);
            //dd('has pg');
        }
        else{// add pg
            $params = ['pg'=>$pg];
            $newUrl = $this->addParamsUrl($url,$params);
        }

    }

    public function resetPagesGroup()
    {
        \Session::forget('user_search_pg');
    }


    public function addParamsUrl($url,$params)
    {
        return $url.http_build_query($params);
    }

    public function requestHasPagination(Request $request)
    {
        $params = $request->query();
        if(array_key_exists('pa',$params))
            return true;
        return false;
    }


    public function addParams( &$request, $params=['p'=>1])
    {

         $request->request->add($params);
         dd($request->fullUrl());

    }

    public function findParentInventory($zipcode,$dist)
    {
        $start = $dist + 1;
        $end = $dist + 10;
        if($end > $this->maxDist) $end = $this->maxDist;
        $cacheName = $this->getCacheName($zipcode,$start);
        $found = [$cacheName=>$dist];
        for($i = $this->maxDist; $i >= $end; $i--){
            $cacheName = $this->getCacheName($zipcode,$i);
            $found = [$cacheName=>$i];

            $inventory = $this->getRawCacheItem($cacheName);
            if(!empty($inventory)){
                break;
            }
        }
        return $found;

    }

}
