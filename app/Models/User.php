<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Zipcode;


class User extends Authenticatable
{
    use Notifiable;


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'fname','lname','zipcode','city','state', 'email', 'password',
    ];

    protected $distanceFromZip;

    protected $appends = ['distance'];
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


    public function DistanceFromZip()
    {
       // dd('good!!!');
        $zipCodeModel = new Zipcode();
        $currentZip = \Session::get('current_zip');
        $distance = $zipCodeModel->distanceApart($this->zipcode,$currentZip);
        //dd('ggoood');
        $distance = number_format($distance,4);
        return $distance;
    }

    public function setDistanceFromSelectedZipAttribute()
    {
        //dd('wooow!!');
        $this->distanceFromZip = $this->DistanceFromZip();
    }

    public function getPrefferedAttribute()
    {

    }


}
