<template>
    <div class="search-div">
        <form onsubmit="return false;" >
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                     <zip :zip="this.currentzip" @zipchanged="updateZip"></zip>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <miles :distance="this.cdistance" :maxnum="150" @mileschanged="updateDistance"></miles>
                    </div>
                </div>
                <div class="row" >
                    <div class="col-12" v-if="hasLocation">
                        <span @click="useLocation">
                            <button class="btn btn-default">click to use your zipcode</button>
                        </span>
                    </div>
                    <div class="col-12">
                        <button @click="fetch" class="btn btn-primary">Submit</button>
                    </div>
                </div>

            </div>
        </form>
    </div>

</template>

<script>
    import zip from './formInputs/zipInput.vue';
    import miles from './formInputs/distanceInput.vue';
    import collection from '../mixins/collection';
    export default {
        props: ['currentzip','currentdistance'],
        components: { zip,miles },
        mixins:[collection],
        data(){
            return{
                initDist:this.currentdistance,
                initZip:this.currentzip,
                zipCode: this.currentzip,
                cdistance: this.currentdistance,
                formInput: false,
                info: false,
            }
        },

        methods: {
            useLocation(){
                alert('gret');
            },
            updateZip(zip){
                //alert(zip);
                this.zipCode = zip;
                this.updateFormInput();
            },
            updateDistance(dist){
                this.cdistance = dist;
                this.updateFormInput();
            },
            updateFormInput(){
                this.formInput = true;
            },

            changedInput() {
               // var hasChanged=false;
                var curData = this.info;
                if(curData){
                    var zipping = curData.zip;
                    var miles = curData.miles;
                    var hasChanged = (this.zipCode != zipping || this.cdistance != miles)
                }else{
                    var hasChanged = true;
                }
                return hasChanged;
            },

            fetch(){
               // alert('clicked now');
               var distchanged = this.cdistance != this.initDist;
               var zipchanged = this.zipCode != this.initZip;

               if(this.changedInput()){
                   this.$emit('processing');
                   var url = '/api/search_users?zip='+this.zipCode+'&dist='+this.cdistance+'&pa=1';
                   this.get(url);

               }
            },

            refresh(data){
                this.info = data.data;
                //if(this.info.length){
                   this.$emit('refreshUsers',this.info);
                //}
            }
        },
        computed:{
            hasLocation(){
                var hasLocation =this.localCache('get','geo_location_available');
                if(hasLocation == 'false')
                    return false;
                return true
            }

        },
        mounted(){
            this.findLocation();
        }


    }

</script>