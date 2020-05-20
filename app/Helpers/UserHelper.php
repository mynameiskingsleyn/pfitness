<?php

namespace App\Helpers;

use App\Helpers\ZipCodeHelper;
use App\Models\User;
use App\Traits\CacheTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

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
    }
    public function getUsersInZip($zipcode='48326',$miles=5)
    {
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
            if(!$usersResult){
                $zipsWithinCode = $this->zipcodeHelper->getZipsNear($zipcode,$distance);
                if($zipsWithinCode){
                    $zips = array_column($zipsWithinCode,'zip');
                    $users = $this->user->whereIn('zipcode',$zips)->orderBy('lname')->get();
                    if($users){
                        //dd($users);
                        //$users=$users->sortBy('lname')->sortBy('distance');
                        //$result=$users->sortBy('distance');
//                    foreach($users as $user){
//                        \Log::info($user->distance);
//                    }
                        //\Log::info('Sorted values below------------------------------->');
                        $result=$users->sortBy(function($value) {
                            $dist = $value->distance;
                            $int = filter_var($dist, FILTER_SANITIZE_NUMBER_INT);
                            $intVal = intval($int);
                            return intval($intVal);
                        });
                        $newUsers = [];
                        foreach($result as $aUser){
                            $newUsers[] = $aUser;
                        }
                        $this->cacheItem($cacheName,$newUsers);

                        //return $newUsers;
                    }

                }
                $usersResult = $this->getCacheItem($cacheName);
            }
            return $usersResult;

        }
        //        $users = $users->sortBy(function($user){
//            return $user->distance;
//        });

        return [];

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
                $next_page = $usersInfo['nextPage'];
                //dd($next_page);
                $prev_page = $usersInfo['prevPage'];
                $page_group = $usersInfo['pageGroup'];
                $current_page=$usersInfo['currentPage'];
                $count = $usersInfo['count'];
            }

        }
        //dd('here yalfffl');
        $finalUsers = collect($users);
        return ['users'=>$finalUsers,'zip'=>$zip,'miles'=>$miles,'next_page'=>$next_page,
            'prev_page'=>$prev_page,'page_group'=>$page_group,  'count'=>$count,'current_page'=>$current_page];
    }

    public function getCacheName($zip,$distance)
    {
        return $zip.'_users_'.$distance;
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



    public function rebuildQueryWithPagination(Request $request,$params=['pa'=>1])
    {

       $url = $request->Url().'?'.http_build_query(array_merge($request->all(),$params));
       return $url;

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

}
