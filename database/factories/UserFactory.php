<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\User;
use Faker\Generator as Faker;
use Illuminate\Support\Str;
use App\Models\Zipcode;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(App\Models\User::class, function (Faker $faker){

    $zipcodes = ['48341','48326','48084','49701','49706','49762', '48044','48154','48187','48416','48830'];
    $states = ['MI','MI','MI','MI','MI','TT','MI','MI','MI','MI','MI'];
    $cities = ['Pontiac','Auburn Hills','Troy','Mackinaw City','Alanson','Naubinway', 'Macomb','Livonia','Canton','Brown City','Elm Hall'];
    $index = array_rand($zipcodes,1);
    $state = Zipcode::WHERE('zip',$zipcodes[$index])->first()->state ?? $states[$index];
    $city = Zipcode::WHERE('zip',$zipcodes[$index])->first()->city ?? $cities[$index];
    //$fakename =$faker->name;
    //$name =  explode(" ",$fakename);

    return [
        'fname' => $faker->word,
        'lname' => $faker->word,
        'email' => $faker->unique()->safeEmail,
        'zipcode' => $zipcodes[$index],
        'city' => $city,
        'state' => $state,
        'country' => 'US',
        'email_verified_at' => now(),
        'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
        'remember_token' => Str::random(10)
    ];

});

$factory->define(App\Models\Job::class, function(Faker $faker){
    return[
        'title'=> $faker->word,
        'description'=> $faker->sentence
    ];

});
