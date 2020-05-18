<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Forum') }}</title>

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <!-- scripts -->
    <script>
        window.App = {!! json_encode([
        'csrfToken' => csrf_token(),
        'signedIn' => Auth::check(),
        'user' => Auth::user()
          ]) !!};
    </script>

    <!--end scripts -->
    <style media="screen">
        body{ padding-bottom:100px; }
        .level{ display: flex; align-items:center;}
        .flex{flex:1;}
        .mr-1{margin-right:1em;}
        .ml-a{margin-left:auto;}
        [v-cloak]{ display:none;}
    </style>
    @yield('header')

</head>
<body>
<div id="app">
    @include('layouts.nav')

    @yield('content')



    @include('_includes._messages')
</div>

<!-- Scripts -->
<script src="{{ asset('js/app.js') }}"></script>
<!-- <script src={{ mix('/js/app.js') }}></script> -->
@yield('scripts')
</body>
</html>
