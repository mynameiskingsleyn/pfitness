<template>
    <div class="input-session">
        <div class="form-group">
            <label for="distance">
                Distance:
            </label>
            <input type="number" class="form-control" name="distance" id="distance" v-model="cDistance" maxlength="5" @keyup="processDistance"
            @change="callChanged" v-bind:min="minNum" v-bind:max="maxNum">
        </div>
    </div>

</template>

<script>

    export default {
        props: ['distance','maxnum','minnum'],

        data() {
            return {
                cDistance: this.distance,
                maxNum : this.maxnum ? this.maxnum : 50,
                minNum: this.minnum ? this.minnum : 0,
            }
        },

        methods: {
            processDistance(){
                var dist = this.cDistance;
                if(dist > this.maxNum){
                    this.cDistance = this.maxNum;
                }
                if(dist < 0){
                    this.cDistance = this.minNum ;
                }
            },
            callChanged(){
                var dist = this.cDistance;
                if(dist == ''){
                    this.cDistance = this.minNum ;
                }
                this.$emit('mileschanged',this.cDistance);
            }

        }

    }

</script>