<template>
    <div>
        <nav aria-label="Page navigation" v-if="shouldPaginate">
            <ul class="pagination">
                <li v-show="hasprevUrl">
                    <a href="#" aria-label="Previous" rel="prev" @click.prevent="getPage(prevpage)">
                        <button class="btn btn-default"><span aria-hidden="true">&laquo;Previous</span></button>
                    </a>
                </li>
                <li v-for="(onepage, name,index) in grouppage">
                    <a href="#" @click.prevent="getPage(onepage)"><button class="btn btn-default" :key="index"
                                                                          data-title="index">{{name}}</button></a>
                </li>
                <li v-show="hasnextUrl">
                    <a href="#" aria-label="Next" rel="next" @click.prevent="getPage(nextpage)">
                        <button class="btn btn-default"><span aria-hidden="true">Next&raquo;</span> </button>
                    </a>
                </li>
            </ul>
        </nav>
        <div class="pageNumber" style="text-align: center;">
            <span v-if="pageNumber != false">
                Page {{ pageNumber }}
            </span>
        </div>
    </div>



</template>

<script>
    import collection from '../mixins/collection';
    export default {
        props:['dataset'],
        mixins:[collection],
        data(){
            return {

                page: window.location.href,
                hasprevUrl: (this.dataset.prevp && this.dataset.prevp.length > 0)? true:false,
                hasnextUrl: (this.dataset.nextp && this.dataset.nextp.length > 0)? true:false,

                currentpage: this.dataset.currentpage,
                nextpage: this.dataset.nextp,
                prevpage: this.dataset.prevp,
                grouppage: this.dataset.groupspa,
                info: false

            }
        },
        watch:{
            // dataSet(){
            //     this.page = window.location.href;
            //     this.prev = this.dataSet.nextp;
            //     this.next = this.dataSet.prevp;
            // },
            dataset: function (val){
              this.grouppage =  this.dataset.groupspa;
              this.nextpage = this.dataset.nextp;
              this.prevpage = this.dataset.prevp;
              this.hasprevUrl = (this.prevpage.length > 0) ? true : false;
              this.hasnextUrl = (this.nextpage.length > 0) ? true : false;
              this.currentpage = this.dataset.currentpage;
            },
            page(){
                this.broadcast().updateUrl();
            }
        },
        computed:{
            shouldPaginate(){
                return  !! this.hasprevUrl || !! this.hasnextUrl;
            },
            pageNumber(){
                var theUrl = this.currentpage;
                var pg =this.extractPageNum(theUrl);
                // return theUrl;
                return pg;

            }
        },
        methods:{
            getPage(url){
                //console.log(url);
                this.$emit('processing');
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

            extractPageNum(page){
                //help  --> https://www.w3schools.com/jsref/tryit.asp?filename=tryjsref_match_regexp
                var res = page.match(/pa=\d{0,10}/g);
                if(res.length > 0)
                {
                    var dign = res[0];
                    var fin = dign.match(/\d+/g);
                    return fin[0];
                }
                else{
                   return false;
                }


            }

        }
    }
</script>
