<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LocalizationController extends Controller
{
    //
    public function index(Request $request, $locale)
    {
        app()->setLocale($locale);

        echo trans('lang.msg');
        echo "<br>";
        echo trans('lang.Good Morning');
    }
}
