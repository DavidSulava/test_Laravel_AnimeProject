<template>

    <div class='filter_section' v-show="v_filter_section" >
        <div class="fButton_wrapper"  >

            <input type="checkbox" class="toggle_f" id="mMenu_f" style="display:none">

            <label for="mMenu_f" class="fButton"  v-on:click = "v_filter_btn = !v_filter_btn">
                <svg viewBox="0 0 24 24" style="height: 0.8em;"><path d="M3,2H21V2H21V4H20.92L14,10.92V22.91L10,18.91V10.91L3.09,4H3V2Z" ></path></svg>
                Filter
            </label>

        </div>


        <div class="filter_wraper" v-show="v_filter_btn" >
            <div class="fOrder">
                Order:
                <button v-on:click="checkdiplay(  'fOrder' )">Default</button>
                <ul  v-show="v_ul_Show.fOrder" >
                    <li class="li fOrder" v-on:click="check_li($event)" v-bind:style="styleObject">Default</li>
                    <li class="li fOrder" v-on:click="check_li($event)">New Update</li>
                    <li class="li fOrder" v-on:click="check_li($event)">Raiting</li>
                    <li class="li fOrder" v-on:click="check_li($event)">Release Year</li>
                    <li class="li fOrder" v-on:click="check_li($event)">Movies name</li>
                </ul>
            </div>

            <div class="fType">
                Type:
                <button v-on:click="checkdiplay(  'fType' )">All</button>
                <ul  v-show="v_ul_Show.fType">
                    <li class="li fType" v-on:click="check_li($event)"  v-bind:style="styleObject" >All</li>
                    <li class="li fType" v-on:click="check_li($event)" >TV</li>
                    <li class="li fType" v-on:click="check_li($event)" >Movie</li>
                </ul>
            </div>

            <div class="fGenre">
                Genre:
                <button v-on:click="checkdiplay(  'fGenre' )">All</button>
                <ul  v-show="v_ul_Show.fGenre">
                    <li class="li fGenre" v-on:click="check_li($event)"   v-bind:style="styleObject"    >All</li>
                    <li class="li fGenre" v-for="g_el in fl_data.genres"  v-on:click="check_li($event)" >{{g_el}}</li>
                </ul>
            </div>

            <div class="fCountry">
                Country:
                <button v-on:click="checkdiplay(  'fCountry' )">All</button>
                <ul  v-show="v_ul_Show.fCountry">
                    <li class="li fCountry" v-on:click="check_li($event)"      v-bind:style="styleObject"    >All</li>
                    <li class="li fCountry" v-for="g_el in fl_data.countries"  v-on:click="check_li($event)" >{{g_el}}</li>
                </ul>
            </div>

            <div class="fYear">
                Year:
                <button v-on:click="checkdiplay(  'fYear' )">All</button>
                <ul  v-show="v_ul_Show.fYear">
                    <li class="li fYear" v-on:click="check_li($event)"  v-bind:style="styleObject"    >All</li>
                    <li class="li fYear" v-for="g_el in fl_data.years"  v-on:click="check_li($event)" >{{g_el}}</li>
                </ul>
            </div>

            <div class="fLang">
                Language:
                <button v-on:click="checkdiplay(  'fLang' )">All</button>
                <ul  v-show="v_ul_Show.fLang">
                    <li class="li fLang" v-on:click="check_li($event)"  v-bind:style="styleObject" >All</li>
                    <li class="li fLang" v-on:click="check_li($event)" >Subbed</li>
                    <li class="li fLang" v-on:click="check_li($event)" >Dubbed</li>
                </ul>
            </div>

            <button style="margin-top : 20px;" v-on:click="get_filteredData($event)">Show</button>

        </div>



    </div>

</template>

<script>
    import {getFetch, v_buss} from '../app';

    export default
    {
        name: "vfilter",
        data()
            {
                return {
                    v_filter_section  : true,
                    v_filter_btn  : false,
                    v_ul_Show     : { fOrder : false, fType: false, fGenre : false, fCountry : false, fYear : false, fLang : false  },
                    styleObject   : { 'background-color':'rgb(37, 181, 121)'},
                    fl_data       : { 'genres': [], 'countries': [], 'years': [] },
                    rootUrl_f     : document.querySelector("meta[name='root_url_f']").getAttribute('content'),
                }
            },
        computed:
            {
                check_li   : function()
                    {
                        return function(ev)
                            {
                                //--hide a li elements after click----
                                ev.target.parentNode.previousElementSibling.innerText = ev.target.innerText;

                                this.v_ul_Show[ev.target.classList[1]]=false;

                                //--change a BG-Color of a li elements
                                ev.target.style.backgroundColor = this.styleObject['background-color']
                                var parent_el = ev.target.parentNode.children;

                                for (const iterator of parent_el)
                                    {
                                        ev.target.innerText != iterator.innerText ? iterator.style.backgroundColor = '' : '';

                                    }

                            }

                    },
                checkdiplay: function()
                    {
                        return function(p_el){


                            for ( var kay in this.v_ul_Show)
                                {
                                    kay != p_el ? this.v_ul_Show[kay]=false : this.v_ul_Show[kay]= !this.v_ul_Show[kay]

                                }

                        }

                    },
                get_filteredData: function()
                    {
                        return async function(evb)
                        {
                            let buttons = evb.target.parentNode.children;
                            let get_str = '';

                            for (const iterator of buttons)
                                {
                                    if(iterator.localName == 'div')
                                    {
                                        // console.log( iterator.outerText);
                                        var [kay, value] = iterator.outerText.split(':');
                                        value.trim();

                                        value.includes('Movies name') ?  value='title'      : '' ;
                                        value.includes('Raiting')     ?  value='rating'     : '' ;
                                        value.includes('Release Year')?  value='start_year' : '' ;
                                        value.includes('New Update')  ?  value='dataUpdated': '' ;


                                        get_str +=`${kay}=${value}&`
                                    }

                                }

                            let url_prepared = this.rootUrl_f.replace(/public[/]?.*/, `public/ajax/get_IndexData?${get_str}`);

                            let resp         = await getFetch( url_prepared, "GET");

                            v_buss.$emit('emit_fd', {'resp':resp, 'q_str':get_str } );
                        }
                    },
            },
        async created()
            {
                let  href_str = this.rootUrl_f.replace( /.*(?<=public[/]?)/, '');

                //---whe filter section not nided--
                this.v_filter_section = href_str.includes("f_status")? false : true;

                if( this.v_filter_section )
                {


                    //---fetch filter categoris---
                    //----[ laravel: request to - ajax/filter ]---------
                    let payload_get = `filterCategories=${true}`;

                    let url_prepared = this.rootUrl_f.replace(/public[/]?.*/, `public/ajax/filter?${payload_get}`);


                    let filter_r  = await getFetch( url_prepared, "GET");


                    'genres' in filter_r  ?  filter_r['genres'].map( (el)=>{ el['genre']  ? this.fl_data.genres.push(el['genre']) : ''  } )      : '';

                    if( filter_r['countries'] )
                        {
                            filter_r['countries'].map( (el)=>{ el['country'] ? this.fl_data.countries.push(el['country']) : ''  } );
                            this.fl_data.countries.sort();
                        }
                    if( filter_r['years'] )
                        {
                            filter_r['years'].map( (el)=>{ el['start_year']  ? this.fl_data.years.push(el['start_year']) : ''  } ) ;
                            this.fl_data.years.sort((f, s)=>{return s- f });
                        }


                }
            },

    }


</script>
