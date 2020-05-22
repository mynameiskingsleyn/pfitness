<template>
    <div v-bind:class="{danger_found: found}">
        <a href="">
            {{ userFName }} {{ userLName }}
        </a>
        about {{ distance }} miles away!!.
    </div>

</template>

<script>

    export default{
        props:['user','found'],

        data(){
            return{
                userFName:this.user.fname,
                userLName: this.user.lname,
                fullName: this.user.fname+' '+this.user.lname,
                distance: this.user.distance
            }
        },
        watch:{
            user: function(val){
                this.userFName=val.fname
                this. userLName= val.lname
                this.fullName=val.fname+' '+val.lname
                this.distance = val.distance
            }
        }

    }
</script>