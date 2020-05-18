<template>
    <div class="row">
        <div class="col-md-4">
            <div class="panel panel-default">
                <div class="panel-heading">Search info</div>
                <div class="panel-body">
                    <userSearchForm :currentzip="currentZip" :currentdistance="currentMiles"
                                    @refreshUsers="searchUpdated" @processing="performLoading">

                    </userSearchForm>
                </div>


            </div>
        </div>
        <div class="col-md-8">
            <usersFound :usersF="currentUsers" :miles="currentMiles" :currentzip="currentZip" :size="userSize"
            :isLoading="loading">
            </usersFound>
        </div>
    </div>
</template>

<script>
    import userSearchForm from '../forms/userSearchForm.vue';
    import usersFound from './Users.vue';

    export default{
        props: ['zip','miles','susers'],
        components:{userSearchForm,usersFound},

        data(){
            return{
                currentUsers:this.susers,
                currentZip:this.zip,
                currentMiles:this.miles,
                ourdata:false,
                loading:false,
                //sortedUsersD:this.sortUsersByDistance(),
            }

        },
        methods:{
            searchUpdated(result){
                if(result){
                    this.loading=false;
                    this.currentUsers = result.users;
                    this.currentZip = result.zip;
                    this.currentMiles=result.miles
                }

            },

            getSize(obj){
                var size = 0, key;
                for (key in obj) {
                    if (obj.hasOwnProperty(key)) size++;
                }
                return size;
            },

            sortUsersByDistance() {
                if(this.users){
                    return this.users.sort((a, b) => {
                        return ( ( a.distance == b.distance ) ? 0 : ( ( a.distance > b.distance ) ? 1 : -1 ) );
                    });
                }
                return null;

            },
            performLoading(){
                this.loading=true;
            }

        },

        computed: {
            userSize(){
                var currentUsers = this.currentUsers;
                return this.getSize(currentUsers);
            },



            // sortedUsers() {
            //     return this.currentUsers.sort((a, b) => {
            //         return ( ( a.distance == b.distance ) ? 0 : ( ( a.distance > b.distance ) ? 1 : -1 ) );
            //     });
            // }
        }


    }


</script>