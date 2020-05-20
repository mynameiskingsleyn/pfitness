@extends('layouts.app')

@section('content')
    <div class="container">

            <users-search :zip="{{ $zip }}" :susers="{{ $users }}" :miles="{{ $miles }}" nextpage="{{ $nextPage }}"  prevpage="{{ $prevPage }}"
            :uscount="{{ $count }}" pagegroup="{{ $pageGroup }}" currentpage="{{ $currentPage }}"
            ></users-search>
            {{--<div class="col-md-4">--}}
                {{--<div class="panel panel-default">--}}
                    {{--<div class="panel-heading">Search info</div>--}}
                    {{--<div class="panel-body">--}}
                        {{--<user-search-form :currentzip="{{ $zip }}" :currentdistance="{{ $miles }} "--}}
                                          {{--@refreshUsers="refresh">--}}

                        {{--</user-search-form>--}}
                    {{--</div>--}}


                {{--</div>--}}
            {{--</div>--}}
            {{--<div class="col-md-8">--}}
                {{--<users :users="{{ $users }}" :miles="{{ $miles }}" v-on:refreshUsers="refresh">--}}
                {{--</users>--}}
                {{--@if(count($users))--}}
                    {{--<div class="panel panel-default">--}}
                        {{--<div class="panel-heading">--}}
                            {{--<strong>{{ $users->count() }} users  </strong>found within <strong> {{ $miles }}</strong> miles of zip {{ $zip }}--}}
                        {{--</div>--}}
                        {{--<div class="panel-body">--}}
                            {{--<ul class="list-group">--}}
                                {{--@foreach($users as $user)--}}
                                    {{--<li class="list-group-item">--}}
                                        {{--<a href="{{ url($user->path())}}">--}}
                                            {{--{{ $user->fname }} {{ $user->lname }}--}}
                                        {{--</a>--}}
                                        {{--about {{ $user->distance }} miles away!!.--}}

                                    {{--</li>--}}
                                {{--@endforeach--}}
                            {{--</ul>--}}


                        {{--</div>--}}
                    {{--</div>--}}
                {{--@else--}}
                    {{--<div class="panel panel-default">--}}
                        {{--<div class="panel-heading">--}}
                            {{--No users found within {{ $miles }} miles of zipcode {{ $zip }}--}}
                        {{--</div>--}}
                    {{--</div>--}}

                {{--@endif--}}
            {{--</div>--}}

    </div>

@endsection
<script>
    // import UserSearch from "../../js/components/userSearch";
    // export default {
    //     components: {UserSearch}
    // }
</script>