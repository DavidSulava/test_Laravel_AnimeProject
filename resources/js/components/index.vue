<template>

    <ul class="m_i_v"  style="list-style-type: none;" >

        <li class="m_data"   v-for="val in m_array.data"  >
            <span v-if="f_del" v-on:click="f_del_remove(val)" class="delMe" :title="m_del_t">❌</span>
            <span v-if="m_del" v-on:click="m_del_remove(val)" class="delMe" :title="m_del_t">❌</span>
            <span  class='m_info_e' v-if= check_length(val) > Eps <i>{{val.episodes}}</i> </span>
            <span  class='m_info_l' > {{ get_translation(val.title) }} </span>
            <a   v-bind:href= 'm_url+val.id' v-bind:style="{'background-image': 'url('+get_img(val)+')' }" class='back_img'>  </a>
            <a   v-bind:href= 'm_url+val.id' class='m_title' > {{get_title (val.title) }}  </a>
        </li>

    </ul>

</template>

<script>
    import {getFetch, v_buss} from '../app';

    export default
    {
        data()
            {
                return {
                        m_url     : 'getMovie?id=',
                        m_array   : [],
                        f_del     : false,
                        m_del     : false,
                        m_del_t   : '',
                        rootUrl_f : document.querySelector("meta[name='root_url_f']").getAttribute('content'),
                    }
            },
        methods:
            {
                check_length   : (el)=>{ return el.episodes > 1 },
                get_translation: (el)=>{ return /(?<=\()(Sub|Dub)(?=\)$)/i.exec(el) ? /(?<=\()(Sub|Dub)(?=\)$)/i.exec(el)[0].toUpperCase() : 'Sub'  },
                get_title      : (el)=>{ return /.*(?=\((Sub|Dub)\)$)/i.exec(el)    ? /.*(?=\((Sub|Dub)\)$)/i.exec(el)[0].trim()           : el },
                get_img        : (el)=>{ return el.imgU2 ? el.imgU2  : el.img ? el.img : 'img/NoImageFound.png' },
            },
        computed:
            {
                //--- del icon
                user_movie_del: function()
                    {
                        return async function( ch_status=false )
                            {
                                let local_href   = this.rootUrl_f;
                                let url_prepared = local_href.replace(/public[/]?.*/, `public/user_check`)

                                //--check autorisation
                                let check_auth =  await getFetch( url_prepared, "GET" );

                                if( check_auth.auth )
                                    {


                                        //--check where we are in index or  favourites
                                        if( local_href.includes('f_status') )
                                            {
                                                this.f_del = true;
                                                this.m_del_t = 'remove from favourites';
                                            }
                                        else
                                            {
                                                this.f_del   = false;
                                                this.m_del   = check_auth.u_type? true : false;
                                                this.m_del_t = 'delete movie';
                                            }

                                    }

                            }
                    },
                //-- del action
                f_del_remove: function()
                    {
                        return async function( movie )
                            {

                                let local_href   = this.rootUrl_f;
                                let m_id         = movie.id ?  movie.id : '' ;

                                //--check del movie entirely or remove from favourites
                                if( local_href.includes('f_status') )
                                    {
                                        //--remove from favourites
                                        let url_prepared = local_href.replace(/public\/.*/, `public/ajax/to_favorite?id=${m_id}`);

                                        let getF =  await getFetch( url_prepared, "GET" );
                                        this.m_array['data'] = this.m_array['data'].filter( el=>{ return el.id != m_id});
                                    }

                            }
                    },
                m_del_remove: function()
                    {
                        return async function( movie )
                            {
                                //--del movie--
                                let local_href   = this.rootUrl_f;
                                let m_id         = movie.id ?  movie.id : '' ;

                                //--check del movie entirely or remove from favourites
                                if( !local_href.includes('f_status') )
                                        {
                                            //--remove from favourites
                                            let url_prepared = local_href.replace(/public\/.*/, `public/ajax/del_movie?id=${m_id}`);

                                            let getF =  await getFetch( url_prepared, "GET" );

                                            if(getF)
                                                this.m_array['data'] = this.m_array['data'].filter( el=>{ return el.id != m_id});

                                        }
                            }
                    }
            },
        async created()
            {
                let url_prepared = this.rootUrl_f.replace(/public[/]?/, 'public/ajax/get_IndexData');

                this.m_array     =  await getFetch( url_prepared, "GET" );

                if( this.m_array &&  this.m_array.data && this.m_array.data.length > 0 )
                    {
                        v_buss.$on('emit_d', (data)=>{this.m_array= data;});

                        v_buss.$emit('emit_index_d', this.m_array );
                    }

                //---[ check favoutite status ]---
                this.user_movie_del(true);
            },

    }


</script>