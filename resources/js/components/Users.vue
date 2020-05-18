<template>
    <div class="">
        <div v-if="gettingData">
           <loading></loading>
        </div>
        <div class="usersDiv" v-else-if="currentUsers">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <strong>{{ size }} users  </strong>found within <strong> {{ miles }}</strong> miles of zip {{ currentzip }}
                </div>
                <div class="panel-body">
                    <ul class="list-group">
                        <li v-for="user in currentUsers" class="list-group-item">
                            <user :user="user"></user>
                        </li>
                        <!--@foreach($users as $user)-->
                        <!--<li class="list-group-item">-->
                        <!--<a href="{{ url($user->path())}}">-->
                        <!--{{ $user->fname }} {{ $user->lname }}-->
                        <!--</a>-->
                        <!--about {{ $user->distance }} miles away!!.-->

                        <!--</li>-->
                        <!--@endforeach-->
                    </ul>


                </div>
            </div>
        </div>
        <div class="noUsersDiv" v-else>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <strong> No users  </strong>found within <strong> {{ miles }}</strong> miles of zip {{ currentzip }}
                </div>
                <div class="panel-body">
                    <strong>Please select different options</strong>
                </div>
            </div>
        </div>
    </div>


</template>

<script>
    import user from './User.vue';
    import User from "./User";

    export default{
        components: {User},
        props:['usersF','miles','currentzip','size','isLoading'],

        data(){
           return {
                //allUsers:this.usersF,
                //zip:this.miles
           }
        },
        computed: {
            gettingData(){
                return this.isLoading;
            },
            currentUsers(){
              return this.usersF;
            }
        },

    }

</script>