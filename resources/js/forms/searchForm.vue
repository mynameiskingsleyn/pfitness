<template>
<div class="search">
    <div class="pull-right">
        <input type="text" class="form-control" name="searchname"
               v-model="searchEntered" @change="getSearcher"
               v-bind:placeholder="searchEntered" style="max-width: 200px">
    </div>
    <div class="search-result" >
        <span class="accordion" >{{searchSentence}}</span>
    </div>
    <div v-if="showOptions">
        <search-list :pages="pages" @updatePages="updatepage"></search-list>
    </div>

</div>
</template>

<script>
    import collection from '../mixins/collection';
    import searchList from '../components/searchPageList';

    export default{
        props:['dataset'],
        mixins:[collection],
        components:{ searchList},
        data(){
            return{
                searchEntered:'Search',
                apiCalled:false,

                // items: ['dolor molestias','occaecati exercitationem','amet aperiam','quia animi'],
                // pages:{ 1:'http://trainme.test/api/search_users?zip=48341&dist=2&pa=1',
                //         4:'http://trainme.test/api/search_users?zip=48341&dist=2&pa=4'
                // },
                items:[],
                pages:{},
                searchName: 'Users',
                currentPage: this.dataset.currentpage
            }
        },
        computed: {
            searchSentence(){
                //if(this.searchEntered && this.searchEntered.toLowerCase() !='search'){
                if(this.apiCalled){
                    if(!this.hasPageOptions())
                        return "No match found";
                    var number = this.getCount(this.items);//this.items.length;
                    var pnumber = this.getCount(this.pages);//Object.keys(this.pages).length;
                    var written = number+' '+this.pluralize(this.searchName,number)+' found in '+pnumber + ' '+this.pluralize('pages',pnumber);
                    return written;
                }
                return '';


            },
            sendGetUrl(){
                return this.getQueryUrl()+'&search='+this.searchEntered;
            },
            showOptions(){
                return this.hasPageOptions();
            }
        },
        methods: {
            updatepage(result){ // used for the paginator on search
                this.$emit('pageupdate',result);
            },

            getSearcher(){ // used for search field call and upda
                if(this.searchEntered.length){
                    var apiUrl = this.sendGetUrl;
                    // call api and update
                    this.$emit('processing');
                    this.get(apiUrl);
                }

                //update items and set pages with response

                // emit with items
              //this.$emit('collectedMatch',this.items);
            },
            getQueryUrl(){
                var curr = this.currentPage;
                var str_exp = '(search_users)';
                var pattern = new RegExp(str_exp,'i');
                var replace = '$1_search';
                if(pattern.test(curr))
                   // alert('match');

                return curr.replace(pattern,replace);
            },

            refresh(data){
                console.log(data.data);
                //alert('good daty');
                this.items = data.data.items;
                this.pages = data.data.pages;
                this.$emit('collectedMatch',this.items);
                this.apiCalled=true;
                //}
            },
            hasPageOptions(){
                if(this.pages)
                    return (this.getCount(this.pages) > 0);
                return false;
            }



        }


    }
</script>