<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Zipcode;
use App\Traits\CacheTrait;


class User extends Authenticatable
{
    use Notifiable;
    use CacheTrait;


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'fname','lname','zipcode','city','state', 'email', 'password',
    ];

    protected $distanceFromZip;

    protected $appends = ['distance','fullname','preferred','expired'];
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function UserJob()
    {
        return $this->hasMany(UserJob::class,'user_id');

    }

    public function getUsersinSearchZip_copy($zipCode,$radius)
    {
        //dd('here now');
        $zip = new Zipcode()
;       //$zipCodes=null;
        $currentZip = $zip->getZipInfo($zipCode);
        //dd($currentZip);
        if($currentZip) {
            $lat = $currentZip->latitude;
            $lon = $currentZip->longitude; //dd($lon);


            \DB::enableQueryLog();
            //$zips = \DB::table('zipcodes')
            $users = $this
                ->Join('zipcodes as z','z.zip','=','users.zipcode')
                ->select(\DB::raw("z.zip,fname, lname, concat(fname,' ',lname) as fullname, users.city, users.state,
                SQRT(POWER(69.1 * (z.latitude - $lat), 2) + POWER(69.1 * ($lon - z.longitude) * COS(z.latitude / 57.3), 2)) AS `distance`"))
                ->having('distance','<=',$radius)
                ->orderBy('distance','asc')
                ->get();
//
            \Log::debug(\DB::getQueryLog());
        }
        return $users;
    }

    public function getUsersInSearchZip($zipCode,$radius)
    {
        //dd('here now');
        $zip = new Zipcode();
        $users = $zip->getZipCodesWithUsers($zipCode,$radius);
        if(empty($users)){
            $users = $this->where('zipcode',$zipCode)->get();
        }
        return $users;
    }

    public function getExpiredAttribute()
    {
        return $this->getExpiredDays();

    }


    public function Zip()
    {
        return $this->belongsTo('\App\Models\Zipcode','zip','zipcode');
    }

    public function getDistanceAttribute()
    {
        return $this->DistanceFromZip();
    }

    public function getFullnameAttribute()
    {
        return $this->fname.' '.$this->lname;
    }


    public function DistanceFromZip()
    {
       // dd('good!!!');
        $zipCodeModel = new Zipcode();
        $currentZip = \Session::get('current_zip');
        //dd($currentZip);
        $distance = $zipCodeModel->distanceApart($this->zipcode,$currentZip);
        $distance = $distance ?? 0;
        //dd('ggoood');
        //$distance = number_format($distance,1);
        //dd($distance);
        return $distance;
    }

    public function setDistanceFromSelectedZipAttribute()
    {
        //dd('wooow!!');
        $this->distanceFromZip = $this->DistanceFromZip();
    }

    public function PreferredUser()
    {
        return $this->hasOne(PrefferedUser::class,'user_id');
    }


    public function getPreferredAttribute()
    {
        //return false;
        $cacheName = $this->getPrefCacheName();
        $preffered = $this->getCacheItem($cacheName);
        if($preffered === null){
            $days = $this->getExpiredDays();
            $preffered = 'false';
            if($days =='bad'){
                $preffered = 'false';
            }elseif($days >= 0){
                $preffered = 'true';
            }else{}
            $this->cacheSet($cacheName,$preffered);
        }
        $preffered = $this->getCacheItem($cacheName);
        return $preffered;


    }

    public function getExpiredDays()
    {
        //dd('here again');
        $cacheName = $this->getExpireDaysCacheName();
        $days = $this->getCacheItem($cacheName);
        if(!$days){
            $now = \Carbon\Carbon::now();
            $preff = $this->PreferredUser;
            $days = 'bad';
            if(is_object($preff)) {
                $expire = \Carbon\Carbon::create($preff->expire_date);
                $days = $expire->diffInDays($now);

            }
        }
        return $days;

    }

    public function path()
    {
        return '/testing';
    }

    public function getPrefCacheName()
    {
        return $this->id.'_prefered';
    }

    public function getExpireDaysCacheName()
    {
        return $this->id.'_expire_days';
    }

    public function getCacheItem($cacheName)
    {
        return $this->cacheGet($cacheName);
    }


}
