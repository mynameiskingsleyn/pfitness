<?php

namespace App\Helpers;

use App\Helpers\ZipCodeHelper;
use App\Models\User;

class UserHelper extends BaseHelper
{
    public function __construct(User $user, ZipCodeHelper $zipCodeHelper)
    {
        $this->user = $user;
        $this->zipcodeHelper = $zipCodeHelper;
    }
    public function getUsersInZip($zipcode,$miles)
    {
        $zipsWithinCode = $this->zipcodeHelper->getZipsNear($zipcode,$miles);
        //dd($zipsWithinCode);
        //dd('here');
        $zips = array_column($zipsWithinCode,'zip');
        $users = $this->user->whereIn('zipcode',$zips)->get();
        return $users;
    }
}
