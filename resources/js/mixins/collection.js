export default{
    data(){
        return {
            items:[]
        }
    },
    methods: {
        get(url){
            axios.get(url)
                .then(this.refresh)
        },

        getCount(item){

            if(item){
                if((typeof item === 'function') || (typeof item === 'object') && !(item === null)){
                    return Object.keys(item).length;
                }
                else
                    item.length;
            }else{
                return 0;
            }

        }
    }
}