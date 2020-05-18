<template>
    <nav aria-label="Page navigation" v-if="shouldPaginate">
        <ul class="pagination">
            <li v-show="prevUrl">
                <a href="#" aria-label="Previous" rel="prev" @click.prevent="page--">
                    <span aria-hidden="true">&laquo;Previous</span>
                </a>
            </li>
            <li v-for="(onepage,index) in pageNumbers">
                <a href="#" @click.prevent="page=index+1">{{index+1}}</a>
            </li>
            <li v-show="nextUrl">
                <a href="#" aria-label="Next" rel="next" @click.prevent="page++">
                    <span aria-hidden="true">Next&raquo;</span>
                </a>
            </li>
        </ul>
    </nav>

</template>

<script>
    export default {
        props:['dataSet'],
        data(){
            return {
                page:1,
                prevUrl: false,
                nextUrl: false
            }
        },
        watch:{
            dataSet(){
                this.page = this.dataSet.current_page;
                this.prevUrl = this.dataSet.prev_page_url;
                this.nextUrl = this.dataSet.next_page_url;
            },
            page(){
                this.broadcast().updateUrl();
            }
        },
        computed:{
            shouldPaginate(){
                return  !! this.prevUrl || !! this.nextUrl;
            },
            pageNumbers(){
                let pages = ['nothing'];
                for(var i=1; i<= this.dataSet.last_page;i++){
                    let loc = location.pathname+'?page='+i;
                    pages[i]=loc;
                }
                pages.shift();
                return pages;
            }
        },
        methods:{
            broadcast(){
                return this.$emit('updated',this.page);

            },
            updateUrl(){
                history.pushState(null, null,'?page='+this.page);
            }
        }
    }
</script>
