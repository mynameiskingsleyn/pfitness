<?php

namespace App\Helpers;

use App\Traits\CacheTrait;

use App\Models\Zipcode;

class BaseHelper
{

    use CacheTrait;
    protected $cachePreFix;
    protected $executeSetting;


    public function __construct()
    {
        $this->cachePreFix = config('trainers.cache_prefix');
        $this->zipModel = new Zipcode();
        $this->executeSetting = true;
    }


    public function getCacheDBKey()
    {
        return $this->cachePreFix['cacheDB'];
    }


    public function addToCacheDB($field,$value)
    {
        $cacheDBKey = $this->getCacheDBKey();
        $cachedVals = $this->cacheHMGet($cacheDBKey,'zipcode');


    }

    /**
     * @param $request
     * @param array $items
     * @return array
     * Function to break items down to pagenate
     */
    public function pagenateItem($request,$items=[])
    {
        $start =$this->getTime();
        if(!$request->has(''))
        $nextPageUrl = null;
        $prevPageUrl = null;
        $result = [];
        $p = $request->get('pa')?? 1;
        $np = $p+1;
        $pp =($p-1 > 0) ? $p-1 :'';
        $cPage = $this->rebuildQueryWithPagination($request,[]);
        if(!$request->has('pa')){
            $cPage = $this->rebuildQueryWithPagination($request);
        }

        //dd('here');
        //dd($cPage);
        if($items){
            $count = count($items);
            $hasNext = $this->hasNextPage($items,$p);
            $hasCurrent = $this->hasItemPage($items,$p);
            //dd($hasCurrent);
            if(!$hasCurrent){
                return ['users'=>[],'count'=>$count];
            }
            $currentPageUrl = $this->addApiToUrl($cPage);
            //dd($currentPageUrl);
            //dd($hasNext);
            if($hasNext){
                $nextPageUrl = $this->updatePageNum($cPage,$np);
                $nextPageUrl = $this->addApiToUrl($nextPageUrl);
            }
            if($pp){
                $prevPageUrl = $this->updatePageNum($cPage,$pp);
                $prevPageUrl = $this->addApiToUrl($prevPageUrl);
            }
            //dd($prevPageUrl);
            //dd('here npwss');
            $numberOfPages = $this->getNumberOfPages($items);
            //dd($request->fullUrl());
            $pagesGroup = $this->getPagesGroup($request,$numberOfPages);
            //dd($pagesGroup);
            //dd($nextPageUrl);
            $users = $this->getCurrentPageData($items,$p);
            $result = ['users'=>$users,'count'=>$count,'nextPage'=>$nextPageUrl,'prevPage'=>$prevPageUrl,
                       'pageGroup'=>$pagesGroup,'currentPage'=>$currentPageUrl];
        }else{
            $result = ['users'=>[],'count'=> 0];
        }
        $end = $this->getTime();
        $timeTaken = $this->timeDiff($start,$end);
        \Log::debug("Time taken to run pagination is $timeTaken");
        //\Log::debug($result);
        return $result;
    }

    public function isApi($request){
        $currentUrl = $request->fullUrl();

        //only if api is called
        $string = '/api';

        if(strpos($currentUrl,$string) !== false){
            return true;
        }
        return false;
    }


    public function getPagesGroup($request, $numberOfPages)
    {

//        $pageGroup = \Session::get('user_search_pg');
//        if($pageGroup){
//            //dd('here');
//            return $pageGroup;
//        }

        $currentUrl = $this->rebuildQueryWithPagination($request,[]);
        //dd($currentUrl);

        //dd($currentUrl);
        $pageGroup = [];
        $cp = $request->get('pa');
        $start = $cp+1;
        $numForward = $cp+$this->pageMaxGroup;
        if($numForward > $numberOfPages)
            $numForward = $numberOfPages;

        for($i=$start;$i<=$numForward; $i++)
        {
            $pageGroup[$i] = $this->updatePageNum($currentUrl,$i);
        }
        //Session::put('user_search_pg',$pageGroup);
        return $pageGroup;
    }

    public function rebuildQueryWithPagination($request,$params=['pa'=>1])
    {

        $url = $request->Url().'?'.http_build_query(array_merge($request->all(),$params));
        return $url;

    }

    public function hasNextPage($items,$p)
    {
        $size = $this->perPage;
        $item_size = count($items);
        $current_position = $size * $p;
        if($current_position < $item_size){
            return true;
        }
        return false;

    }

    public function hasItemPage($items,$page)
    {
        if($page == 0){
            return false;
        }
        $size = $this->perPage;
        $item_size = count($items);
        $current_position = $size * $page;
        return $current_position <= $item_size;

    }

    public function updatePageNum($url,$page)
    {
        $pNum = 'pa='.$page;
        $pattern = '/pa=\d{0,10}/';

        $newPage_url = preg_replace($pattern,$pNum,$url);

        $newPage_url = $this->addApiToUrl($newPage_url);
        //dd($newPage_url);
        return $newPage_url;
    }

    public function getCurrentPageData($items, $p)
    {
        //dd($items);
        $itemPerPage = $this->perPage;
        $item_size = count($items);
        $start = ($p-1) * $itemPerPage;
        $end = $start + $itemPerPage;
        $data = [];
        for($i = $start; $i<$end; $i++)
        {
            $data[] = $items[$i];
        }
        return $data;
    }

    public function getUsersPageNumbers($items,$spots)
    {
        $pageNumbers = [];
        //dd($spots);
        $itemPerPage = $this->perPage;
        $items_size = count($items);
        $numbOfPages = intval($items_size/$itemPerPage);
        //dd($numbOfPages);
        $mapper = [];
        $counter = 1;
        for($k=0;$k< $numbOfPages;$k++){
            $pageNum= $k+1;
            $start = $k * $itemPerPage;
            $end = $start + $itemPerPage;
            $grouped = [];

            for($i=$start; $i<$end; $i++){
                array_push($grouped,$i);
            }
            $mapper[$pageNum] = $grouped;
        }
        //dd($mapper);
        $spotPages =[];
        foreach($spots as $spot){
            foreach($mapper as $key=> $map)
            {
                if(in_array($spot,$map)){
                    array_push($spotPages,$key);
                }
            }
        }
        return array_unique($spotPages);
    }

    public function getNumberOfPages($items)
    {
        $itemPerPage = $this->perPage;
        $item_size = count($items);
        $numbOfPages = intval($item_size/$itemPerPage);
        return $numbOfPages;
    }

    // end pagination


    ///caching functions
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



    function executeSettings()
    {
        if($this->executeSetting == true){
            ini_set('max_execution_time', 0);
            set_time_limit(0);
            ini_set('memory_limit', -1);
        }
    }

    public function addApiToUrl($url)
    {
        //dd($url);
        $string = '/api';
        $hasApi = strpos($url,$string);
        if($hasApi == false){
            $urlArray = explode('/',$url);

            $main = $urlArray[2];
            $newMain = $main.$string;
            $urlArray[2] = $newMain;
            $newUrl = implode('/',$urlArray);
            return $newUrl;
        }
        return $url;

    }

    /**
     * @param array $group group of items to search through
     * @param array $keys  group of keys to search for matches
     * @param $returnname  what to return to api for api match front end. full name
     * @return mixed
     */
    public function findMatches($group=[],$keys=[],$returnname,$search='Searching')
    {
        //dd($search);
        $matchedGroup = [];
        foreach($group as $key=>$anItem){
            foreach($keys as $look){
                if(isset($group[$key][$look])){
                    if(strpos($group[$key][$look],$search) !== False){
                        $matchedGroup[$key]= $group[$key][$returnname];
                    }
                }
            }
        }
        return $matchedGroup;
    }

    /**
     * @return \Carbon\Carbon
     * geting current time
     */
    public function getTime(){
        return \Carbon\Carbon::now();
    }

    /**
     * @param $first
     * @param $second
     * @return mixed
     * returns difference between time.
     */
    public function timeDiff($first, $second)
    {
        return $first->diffInMilliSeconds($second);
    }
    public function clearAllCache()
    {
        $this->CacheFlushAll();
    }


}