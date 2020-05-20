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
            :isLoading="loading" :next="np" :prev="pp" :groupp="pgroup" :fcount="scount"
            v-bind:cpage="currentp" @pagefullupdate="searchUpdated" >
            </usersFound>
        </div>
    </div>
</template>

<script>
    import userSearchForm from '../forms/userSearchForm.vue';
    import usersFound from './Users.vue';

    export default{
        props: ['zip','miles','susers','nextpage','prevpage','pagegroup','uscount','currentpage'],
        components:{userSearchForm,usersFound},

        data(){
            return{
                currentUsers:this.susers,
                currentZip:this.zip,
                currentMiles:this.miles,
                ourdata:false,
                loading:false,
                pgroup: this.convertToObject(this.pagegroup),
                pp: this.prevpage,
                np: this.nextpage,
                scount: this.uscount,
                currentp: this.currentpage

                //sortedUsersD:this.sortUsersByDistance(),
            }
        },
        methods:{
            searchUpdated(result){
                if(result){
                    //alert('earch update');
                    //console.log(result);
                    this.loading=false;
                    this.currentUsers = result.users;
                    this.currentZip = result.zip;
                    this.currentMiles=result.miles;
                    this.scount = result.count;
                    this.currentp = result.currentpage
                    this.np =result.nextpage;
                    this.pp =result.prevpage;
                    this.pg =result.pagegroup;
                    this.pgroup = this.convertToObject(result.pagegroup);
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
            },

            convertToObject(str){
                if (typeof str === 'string' || str instanceof String){
                    // convert to object
                    var obj = JSON.parse(str);
                    return obj;
                }
                return str;
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