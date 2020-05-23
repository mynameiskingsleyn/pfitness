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

    protected $appends = ['distance','fullname','preferred'];
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
        $distance = $zipCodeModel->distanceApart($this->zipcode,$currentZip);
        //dd('ggoood');
        $distance = number_format($distance,1);
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
        $checker = $this->getPrefCacheItem($cacheName);
        if($checker === null){
            $now = \Carbon\Carbon::now();
            $preff = $this->PreferredUser;
            if(is_object($preff)) {
                $expire = \Carbon\Carbon::create($preff->expire_date);
                $check = $now->diffInDays($expire);
                $checker = ($check <= 0) ? 'true': 'false';
            }else {
                $checker = 'false';
            }
            $this->cacheSet($cacheName,$checker);
        }
        //dd($checker);
        return $checker;


    }

    public function path()
    {
        return '/testing';
    }

    public function getPrefCacheName()
    {
        return $this->id.'prefered';
    }

    public function getPrefCacheItem($cacheName)
    {
        return $this->cacheGet($cacheName);
    }


}
