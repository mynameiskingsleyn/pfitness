<template>
    <div class="input-session">
        <div class="form-group">
            <label for="zipcode">
                zipcode:
            </label>
            <input type="text" name="zipcode" class="form-control" id="zipcode" v-model="zipCode" maxlength="5" @keyup="getMatchData"
            @change="callChanged">
        </div>
        <div class="zip_suggest" v-if="hasSuggestions()" id="ziplist">
            <ul class="dropdown-menu auto-fill-ul" style="">
                <li v-for="(aZip, index) in suggestions.zips" :key="zip+index" @click="updateZip(index)"
                style="cursor:pointer">
                    {{ aZip }}
                </li>

            </ul>
        </div>
    </div>

</template>

<script>
    import collections from './mixins/collection';
    export default{

        props:['zip'],
        mixins:['collections'],

        data(){
            return{
                zipCode:this.zip,
                maxLen: 5,
                suggestions:false,

            }
        },

        methods: {
            getMatchData(){
               var zipcode = this.cleanZip(this.zipCode);
               var count = zipcode.length;
               if(count < 5 && count > 2){
                   // run suggestions.
                   axios.get('/api/auto_complete_zip?zipPart='+zipcode)
                       .then(response=> this.suggestions = response.data)
               }
                return null;
            },
            cleanZip(){
                var zipn = this.zipCode;
                var zips = zipn.toString();
                var zipr = zips.replace(/\D/g,'');
                this.zipCode = zipr;
                //alert(zipr);
                return zipr;
            },
            clearMatch(){
                this.suggestions=false;
            },
            updateZip(id){
                //alert('clicked suvvestion yall');
                var selZip = this.suggestions.zips[id];
                this.zipCode=selZip;
                this.callChanged();
                //this.clearMatch();
            },

            callChanged(){
                this.cleanZip();
                //this.clearMatch();
                if(this.zipCode.length == 5){
                    this.clearMatch();
                }
                this.$emit('zipchanged',this.zipCode);
            },

            hasSuggestions(){
                var ans = false;
                if(this.suggestions){
                    var sug = this.suggestions.zips;
                    if(sug.length > 1){
                        ans = true;
                    }
                }
                return ans;
            }



        }
    }

</script>