<?php

namespace App\Helpers;

use App\Helpers\ZipCodeHelper;
use App\Models\User;
use App\Traits\CacheTrait;
use Illuminate\Http\Request;

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
        if($this->pagenate){
            $users = $this->pageinateItem($request,$users);
        }

        $finalUsers = collect($users);
        return ['users'=>$finalUsers,'zip'=>$zip,'miles'=>$miles];
    }

    public function getCacheName($zip,$distance)
    {
        return $zip.'_users_'.$distance;
    }

    // general cache functions=======
    public function cacheItem($cacheName, $value)
    {
        $this->cacheSet($cacheName,json_encode($value));
    }
    public function getCacheItem($cacheName)
    {
        $items = $this->cacheGet($cacheName);
        return json_decode($items,true);
    }
    // complex


    public function getHCacheItem($cacheName)
    {
        $items = json_decode($this->cacheHMGetAll($cacheName));
        return $items;
    }
    public function setHCacheItem($cacheName,$value,$count=null)
    {
        $this->cacheHMSet($cacheName,'value',json_encode($value));
        if($count){
            $this->cacheHMSet($cacheName,'count',json_encode($count));
        }
    }




    /// end of cache function s

    public function pageinateItem($request,$users)
    {
        $p = $request->get('p');
        dd($p);
        dd($request->all());
    }

    public function rebuildQueryWithPagination(Request $request,$params=['p'=>1])
    {
        $query = array_merge($request->query(),
            $params);
        return url()->current().'?'.http_build_query($query);
    }

    public function requestHasPagination(Request $request)
    {
        $params = $request->query();
        if(array_key_exists('p',$params))
            return true;
        return false;
    }


    public function addParams( &$request, $params=['p'=>1])
    {

         $request->request->add($params);

    }
}
