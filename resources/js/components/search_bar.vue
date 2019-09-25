<template>
<div>

   <div class="search-content  col-xs-4 col-sm-7  col-md-2 ">

        <div v-on:click="mobBtn()"  class="mobile-search active ">

            <label  for="fa-search" class="fa fa-search "></label>

        </div>

        <div class="inputCont">

            <input @focus="showResults()" @blur="hideResults()" v-on:input="getData($event.target.value)" v-model="search" name="keyword" type="text" class="form-control search-input  col-md-3 " placeholder="Type to search...">
            <a class="search-submit  col-md-1" :href="`${v_sub_Btn_path}title=${search}`" title="Search">
                <i class="fa fa-search "></i>
            </a>

        </div>

    </div>


    <div  class="search-suggest row  col-xs-11 col-md-offset-8 col-md-3" style="display: block;">
        <ul style="list-style-type: none;">
            <li v-if='v_content[0].id' v-for='(val, index) in v_content'>

                <a :href="`getMovie?id=${val.id}`" :style="`background-size: cover; display: block; width: 50px; height: 70px; background-image: url(${val.img});`"></a>

                <div>
                    <a :href="`getMovie?id=${val.id}`"> {{ val.title }} </a>
                    <p v-show="val.media_type" > {{ val.media_type }} </p>
                    <p v-show="val.start_year" > {{ val.start_year }} </p>
                </div>

            </li>
            <li v-if='v_content'   class="ss-bottom" style="padding: 0px; border-bottom: none; border-radius: 0px 0px 5px 5px;">
                <a id="finde_all" :href="`${v_sub_Btn_path}title=${search}`" >View all</a>
            </li>
        </ul>
    </div>

    <div v-show="v_mobl_btn" class="wrapper_MbSearch row  col-xs-12">
        <div   class="inputCont mobileBar" >
                <input @focus="showResults()" @blur="hideResults()" v-on:input="getData($event.target.value)" v-model="search" name="keyword" type="text" class="form-control search-input" placeholder="Type to search...">
                <a class="search-submit " :href="`${v_sub_Btn_path}title=${search}`" title="Search"><i class="fa fa-search"></i></a>
        </div>
    </div>


</div>
</template>

<script>
    import {getFetch} from '../app';

    export default {
         data()
            {
                return {

                    v_content  : null,
                    search     : null,
                    v_mobl_btn : false,
                    v_sub_Btn_path : "/project/public/?",

                }
            },
        computed:
            {
                getData:function  ( )
                    {
                        return async function(title)
                            {
                                if( title && title.length > 2 )
                                    {
                                        let rootUrl_f    = document.querySelector("meta[name='root_url_f']").getAttribute('content');
                                        let url_prepared = rootUrl_f.replace(/public.*/, `public/search?title=${title}` );

                                        let getM =  await getFetch(url_prepared, "GET", );

                                        if ( getM )
                                            {
                                                this.v_content=getM;
                                            }
                                        else
                                            {
                                                this.v_content=null;
                                            }
                                    }
                                 else
                                    {
                                        this.v_content=null;
                                    }

                            }
                    },
                mobBtn:function()
                    {
                        return function()
                            {
                               this.v_mobl_btn = !this.v_mobl_btn;

                               if ( !this.v_mobl_btn  )
                                    {
                                        //-- search results disapear
                                        this.search    = null;
                                        this.v_content = null;
                                    }

                            }
                    },
                showResults:()=>
                    {
                        return ()=>
                            {
                                let results       = document.querySelector( '.search-suggest ul' );
                                    results.style = 'display:block;'
                            }
                    },
                hideResults:function  (  )
                    {

                        return function()
                            {
                                document.onclick= function(event)
                                    {
                                        let results = document.querySelector( '.search-suggest ul' );

                                        if( !results.innerHTML.includes(event.target.innerHTML) )
                                                results.style= 'display:none;'

                                    }
                            }
                    },
            }
    }
</script>
