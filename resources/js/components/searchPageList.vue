<template>
    <div>
        <select name="selpage" id="selpage" v-model="selectedpage" @change="actNow"
        class="form-control"
                onfocus='this.size=4;' onblur='this.size=1;' onchange='this.size=1; this.blur();'>
            <option value="">
                select pages
            </option>
            <option v-for="(apage,name,index) in fpages" v-bind:value="apage">
                page {{ name }}
            </option>
        </select>
    </div>

</template>

<script>
    import collection from '../mixins/collection';
    export default{
        props:['pages'],
        mixins:[collection],
        data(){
            return {
                fpages: this.pages,
                selectedpage: false
            }
        },
        methods:{
            getPage(url){
                //console.log(url);
                this.$emit('processing');
                //alert('page set');
                this.get(url);
                // axios.get(url)
                //     .then(this.refresh)

            },

            refresh(data){
                //alert('good daty');
                this.info = data.data;
                //if(this.info.length){
                this.$emit('updatePages',this.info);
                //}
            },

            actNow(){
                if(this.selectedpage.length){
                    this.getPage(this.selectedpage);
                }
            },

        },
        watch:{
            pages: function(val){
                this.fpages = val;
            }
        }
    }
</script>