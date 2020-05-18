<template>
    <div>
        <a href="">
            {{ userFName }} {{ userLName }}
        </a>
        about {{ distance }} miles away!!.
    </div>

</template>

<script>

    export default{
        props:['user'],

        data(){
            return{
                userFName:this.user.fname,
                userLName: this.user.lname,
                fullName: this.user.fname+' '+this.user.lname,
                distance: this.user.distance
            }
        }
    }
</script>