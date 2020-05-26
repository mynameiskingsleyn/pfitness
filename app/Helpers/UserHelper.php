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
        $this->userModel = $user;
        $this->zipcodeHelper = $zipCodeHelper;
        $this->pagenate = config('trainers.pagination.pagenate');
        $this->perPage = config('trainers.pagination.perpage');
        $this->pageMaxGroup = config('trainers.pagination.maxGroup');
        $this->defaults = [
            'miles'=>config('trainers.defaults.dist'),
            'zipcode'=>config('trainers.default.zip')
        ];
        $this->zipcodeKeyBase = 'zipcodes';
        $this->preferedKeyBase='prefered';
        $this->maxDist = 100;
    }
    public function getUsersInZip($zipcode='48326',$miles=5)
    {

        $startTime = $this->getTime();
        //$this->clearAllCache();
        $distance = $miles ?? 2;
        if($distance > 150){
            $distance = 150;
        }
        if($zipcode){
            $this->zipcodeHelper->setCurrentZip($zipcode);
        }
        $zipcode = $zipcode ?? $this->zipcodeHelper->getCurrentZip();
        if($zipcode){

            $cacheName = $this->getCacheName($zipcode,$distance);
            $usersResult = $this->getCacheItem($cacheName);
            //dd($usersResult
            if(!$usersResult){
                //$usersNear = $this->zipcodeHelper->getUsersNearMe($zipcode,$distance);
                $usersNear = $this->userModel->getUsersInSearchZip($zipcode,$distance);
                if($usersNear) {
                    if(!is_array($usersNear)){
                        $usersNear = $usersNear->toArray();
                    }
                    //dd($usersNear);

                    //dd($usersNear);
                    //$users = $this->extractUsersFromZips($zipswithUsersWithinCode);
                    //dd($users);
//                    $zips = array_column($zipsWithinCode, 'zip');
//                    $pInventory = $this->findAnInventory($zipcode,$distance);
//                    if(isset($pInventory['cacheName'])){ // possible inventory found..
//                        // check whether child or parent
//                        $isParent = $pInventory['parent'];
//
//                        if($isParent){// not much work needed use parent to capture
//                            $jsonInventory = $this->getRawCacheItem($pInventory['cacheName']);
//                            $newUsers = $this->getUserSearchWithInventory($jsonInventory,$zips);
//                            $this->cacheItem($cacheName,$newUsers);
//                        }else{
//                            //dd('need to create from child');
//                            $childZips = $pInventory['zips'];
//                            $childInventory = $this->getCacheItem($pInventory['cacheName']);
//                            $zipsOver = array_diff($zips,$childZips);
//                            $newInventory = $this->getUserFromDB($zipsOver);
//                            //$newInventory = json_decode(json_encode($newInventory),true);
//                            $parentInventory = array_merge($childInventory,$newInventory);
//                            $itemsCount = count($parentInventory);
//                            $cacheStart = $this->getTime();
//                            $this->cacheItem($cacheName,$parentInventory);
//                            $cacheEnd = $this->getTime();
//                            $cacheComplete = $this->timeDiff($cacheStart,$cacheEnd);
//                            //\Log::debug("time taken for caching $cacheName with $itemsCount items is $cacheComplete");
//                        }
//
//                    }else{// no inventory treat as first search on zip.
//                        $this->createInventory($zipcode,$cacheName,$distance,$zips);
//                    }

                    //else just retrive user and cache user

                    $this->cacheItem($cacheName,$usersNear);
                }

                $usersResult = $this->getCacheItem($cacheName);
                //dd($usersResult);
            }

            //dd($usersResult);
            $endTime = $this->getTime();
            $completTime = $this->timeDiff($startTime,$endTime);
            \Log::debug("time taken to get  all user for $zipcode is $completTime ");
            //dd($usersResult);
            return $usersResult;

        }
        return [];

    }

    public function extractUsersFromZips($zipGroup)
    {
        $usersStack = [];
        foreach($zipGroup as $zips){
            //dd($zips);
            $distance = $zips->distance;
            //dd($distance);
            $zipusers = $zips->users;
            if($zipusers->count() > 0){
                foreach($zipusers as $user){
                    $user->distance = $distance;
                    $usersStack[] = $user->toArray();
                    //dd($usersStack);
                }
            }

        }
        return $usersStack;
    }
/* not being used at the moment
    public function getUserSearchWithInventory($jsonInventory,$zips)
    {
        // convert to string and back to json to add table name;
        $startTime = $this->getTime();
        $arrayInvent = json_decode($jsonInventory,true);
        $usersG = ['users'=>$arrayInvent];
        $data = json_encode($usersG);
        //dd($data);

        $q = new Jsonq();
        $users = $q->json($data)
                    ->from('users')
                    ->whereIn('zipcode',$zips)
                    ->get();
        $endTime = $this->getTime();
        $complete = $this->timeDiff($startTime,$endTime);
        //\Log::debug("Time take to retrive users using inventory for sort zip is $complete");
        return $users;
    }
    public function createInventory($zipcode,$cacheName,$dist,$zips)
    {
        \Log::info("-----------Creating new search Inventory for $cacheName ------------------>");
        $start = $this->getTime();
       // \Log::debug("inventory cache used is $cacheName and distance is $dist");
        $users = $this->getUserFromDB($zips);
        $count = 0;
        if($users){
            $cacheStart = $this->getTime();
            $this->cacheItem($cacheName,$users);
            $count = count($users);
            $cacheEnd = $this->getTime();
            $cacheComplete = $this->timeDiff($cacheStart,$cacheEnd);
           // \Log::debug("time taken to cache and count users from Zipcod $zipcode with count $count into cache $cacheName is $cacheComplete");
        }
        $end = $this->getTime();
        $complete = $this->timeDiff($start,$end);
        \Log::debug("time used for creating new Inventory $cacheName for $zipcode with distance $dist is $complete .milsec");
        \Log::info('---------------------inventory function ended!!----------------------');
    } */

//    /**
//     * @param $zips
//     * @return array|bool
//     * Gets and sort users from db.
//     */
    /* not being used at the moment
    public function getUserFromDB($zips)
    {
        $start = $this->getTime();
        $users = $this->user->whereIn('zipcode',$zips)->get();
        $end = $this->getTime();
        $complete = $this->timeDiff($start,$end);
        //\Log::debug("Time taken to get users from DB is $complete");
        if($users){
            $newUsers = $this->sortUsers($users);
            return $newUsers;

        }
        return false;
    }
*/
    /**
     * @param $request
     * @return mixed
     * camputures matches for user in search input
     */
    public function getUsersSearch($request)
    {
        $miles = $request->get('dist') ?? $this->defaults['miles'];

        $zip = $request->get('zip') ?? $this->defaults['zipcode'];
        //dd('here');
        $users = $this->getUsersInZip($zip,$miles);
        //dd($users);
        $search = $request->get('search') ?? '';
        $search = strtolower($search);

        //dd($users);
        $usersLocate = $this->findMatches($users,['fullname'],'fullname',$search);
        //dd($usersLocate);
        //extract location for creating pages
        $spots = array_keys($usersLocate);
        $names = array_values($usersLocate);
        //generate pages using positons
        $pagenumbers = $this->getUsersPageNumbers($users,$spots);
        //dd($pagenumbers);
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
        //dd($users);
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
            //dd($usersInfo);
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
        return $this->zipcodeKeyBase.':'.$zip.'_users_'.$distance.'_'.$jobCode;
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
//not being used.
//    public function findAnInventory($zipcode,$dist)
//    {
//        $startTime = $this->getTime();
//        $found = [];
//        for($i = $this->maxDist; $i >0 ; $i--){
//            $cacheName = $this->getCacheName($zipcode,$i);
//            $inventory = $this->getRawCacheItem($cacheName);
//            if(!empty($inventory)){
//                $isParent = $i > $dist;
//                $zips = [];
//                if(!$isParent){
//                    $zipsWithinCode = $this->zipcodeHelper->getZipsNear($zipcode,$i);
//                    if($zipsWithinCode) {
//                        $zips = array_column($zipsWithinCode, 'zip');
//                    }
//                }
//                $found = ['cacheName'=>$cacheName, 'parent'=>$isParent, 'zips'=>$zips];
//                break;
//            }
//        }
//        $endTime = $this->getTime();
//        $completeTime = $this->timeDiff($startTime,$endTime);
//        \Log::debug("time takem to find Inventory for zip  $zipcode is $completeTime");
//        return $found;
//
//    }

    /**
     * @param $users
     * @return array
     * Sort users to be used for inventory and future searches.
     */
    public function sortUsers($users)
    {

        if($users){
            //dd($users);
            //Log::info('Sorted values below------------------------------->');
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
            $sortEnd = $this->getTime();
            $sortComplete = $this->timeDiff($sortStart,$sortEnd);
           // \Log::debug("time taken to sort users is $sortComplete");

            $arrangeStart = $this->getTime();
            $newUsers = $result->toArray();
            //$newUsers = array_merge($newUsers);
            $newUsers = array_values($newUsers);
//            foreach($result as $aUser){
//                $newUsers[] = $aUser;
//            }

            $arrangeEnd = $this->getTime();
            $arrangeComplete = $this->timeDiff($arrangeStart,$arrangeEnd);
           // \Log::debug("Time taken to rearange users after sorting is $arrangeComplete");


            //dd($newUsers);

            return $newUsers;

        }
        return [];
    }
    ///// used for cron jobs
    public function getAllOcupiedzips()
    {
        $zips = $this->zipcodeHelper->getOcupied();
        return $zips;
    }

    public function createInventoryForZips(array $zips)
    {
        foreach($zips as $zip){
            $this->getUsersInZip($zip,50);
        }
    }

}
