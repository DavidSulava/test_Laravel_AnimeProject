<template>

   <ul class="pagination"  style="list-style-type: none;" v-if="range.allPages.length > 1" >

        <li class="page-item" :class="{ active:range.current === 1 }" >
            <a   v-bind:href= 'pages.first_page_url'  class='page-link'  v-on:click.prevent="get_page( 1 )" > 1 </a>
        </li>

        <li class="page-item" style="cursor:pointer" v-if="range.allPages.length > 2" >
            <a v-on:click.prevent="get_page( pages.current_page - 1 )" > << </a>
        </li>


        <li class="page-item"  :class="{ active:range.current === index }"   v-for='index in  range.allPages.slice(range.start, range.end )' :key="index+1"   >

            <a   v-bind:href= 'm_url + index + url_get_r'  class='page-link'   v-on:click.prevent="get_page( index )" > {{ index }}  </a>

        </li>


        <li class="page-item" style="cursor:pointer" v-if="range.allPages.length > 2">
            <a v-on:click.prevent="get_page( pages.current_page +1 )" > >> </a>
        </li>

        <li class="page-item" :class="{ active:range.current === pages.last_page }" >
            <a   v-bind:href= 'pages.last_page_url'  class='page-link'  v-on:click.prevent="get_page( pages.last_page )" > {{ pages.last_page }}  </a>
        </li>
    </ul>

</template>

<script>
    import {getFetch, getUrlParams, v_buss} from '../app';

    export default {
         data()
            {

                return {
                    url_get_r : getUrlParams(null, true).replace('?','&'),
                    m_url     : '?page=',
                    pages     : [],
                    len       : 6,
                    range     : { 'start': 0, 'end': 0, 'current': 0, 'allPages': []  }
                }
            },

        methods:{
            get_page: async function (el=1, filter = false )
                {

                    if (this.range.current != el && ( el <= this.pages.last_page &&  el >= 1 ) && !filter )
                        {

                            var v_pages = await getFetch( this.pages.path +this.m_url+el, "GET", null, this.url_get_r );
                            let d_array = v_pages.data;


                            if (d_array.length > 0 && typeof d_array === 'object')
                                {
                                    this.pages = v_pages;

                                    //---Pagination assigning---
                                    if( v_pages.current_page > (this.len/2) && v_pages.current_page <= (v_pages.last_page -(this.len/2) )     )
                                        {
                                            var firs_p = v_pages.current_page - this.len/3;
                                            var last_p = v_pages.current_page < (v_pages.last_page - (this.len/2)) ? v_pages.current_page + (this.len/2): v_pages.last_page;
                                        }
                                    else if( v_pages.current_page <= (this.len/2)  )
                                        {
                                            var firs_p = 2;
                                            var last_p = this.len+1;
                                        }
                                    else if( v_pages.current_page > (this.len/2) && v_pages.current_page > (v_pages.last_page -(this.len/2)) )
                                        {
                                            var firs_p = v_pages.last_page >this.len ? v_pages.last_page - (this.len-1) : 2;
                                            var last_p = v_pages.last_page;
                                        }
                                    //~~~~~~~~~~~~~~~~~~~~~~~~~~
                                    this.range = {'start':firs_p, 'end':last_p, 'current': v_pages.current_page,  'allPages': Array.from( Array(v_pages.last_page).keys() ) };

                                    v_buss.$emit('emit_d', v_pages );
                                } ;

                            return v_pages
                        }
                    if(filter)
                        {
                            var v_pages = this.pages;
                            if (v_pages.data.length > 0 && typeof v_pages.data === 'object' )
                                {
                                    //---Pagination assigning---
                                    if( v_pages.current_page > (this.len/2) && v_pages.current_page <= (v_pages.last_page -(this.len/2) )     )
                                        {
                                            var firs_p = v_pages.current_page - this.len/3;
                                            var last_p = v_pages.current_page < (v_pages.last_page - (this.len/2)) ? v_pages.current_page + (this.len/2): v_pages.last_page;
                                        }
                                    else if( v_pages.current_page <= (this.len/2)  )
                                        {
                                            var firs_p = 2;
                                            var last_p = this.len+1;
                                        }
                                    else if( v_pages.current_page > (this.len/2) && v_pages.current_page > (v_pages.last_page -(this.len/2)) )
                                        {
                                            var firs_p = v_pages.last_page >this.len ? v_pages.last_page - (this.len-1) : 2;
                                            var last_p = v_pages.last_page;
                                        }
                                    //~~~~~~~~~~~~~~~~~~~~~~~~~~
                                    this.range = {'start':firs_p, 'end':last_p, 'current': v_pages.current_page,  'allPages': Array.from( Array(v_pages.last_page).keys() ) };

                                    v_buss.$emit('emit_d', v_pages );

                                } ;

                            return v_pages
                        }
                },

            },
        created()
            {

                v_buss.$on('emit_fd', (data)=>
                    {

                        this.m_url = `?${data.q_str}page=`
                        this.pages= data.resp;
                        this.get_page(1, true);

                    });

                v_buss.$on('emit_index_d', (data)=>
                    {

                        this.pages = data;
                        this.range = {'start':2, 'end':data.current_page + this.len, 'current': data.current_page, 'allPages':  Array.from( Array(data.last_page).keys() ) }

                    });

            }
    }
</script>

