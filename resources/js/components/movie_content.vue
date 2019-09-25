<template>
    <div class="wrapper_content">
        <div class="content_container"  v-if="v_content">
            <span v-on:click="user_favorite()" :class="`favor ${v_favourite} fa-heart`" title="only for registered users"></span>
            <div class="content_title">


                <h2>{{v_content.title}}</h2>
                <span class="trailerButtonWrapper" v-show='v_content.trailer'>
                    <a v-on:click="trailer_diplay()" class="btn btn-primary trailerButton" style="display: block;">
                        <i class="fa fa-video-camera mr5"></i>
                        Trailer
                    </a>
                </span>

            </div>
            <br>

            <div class="img">
                <img v-bind:src="v_img" alt="error">
            </div>

            <div class="info">
                <dl v-if="v_content.aired"     > <dt>Start Year:</dt> <dd> {{ v_content.aired }}     </dd> </dl>
                <dl v-if="v_content.media_type"> <dt>Media type:</dt> <dd> {{ v_content.media_type }}</dd> </dl>
                <dl v-if="v_content.rating"    > <dt>Raiting:   </dt> <dd> {{ v_content.rating }}    </dd> </dl>
                <dl v-if="v_content.episodes"  > <dt>Episodes:  </dt> <dd> {{ v_content.episodes }}  </dd> </dl>
                <dl v-if="v_content.time"      > <dt>Runtime:   </dt> <dd> {{ v_content.time}}       </dd> </dl>
                <dl v-if="v_content.status"    > <dt>Status:    </dt> <dd> {{ v_content.status}}     </dd> </dl>


                <!--Director-->
                <dl class="director" v-if="v_content.director" >
                    <dt>Director:</dt>
                        <dd>
                            <a  v-for='(val, index) in v_content.director' :href="`${v_dl_path}director=${val.name}`" style="cursor: pointer; text-decoration: none;"> {{ val.name }} {{ v_content.director.length-1 == index? '' : ', '}} </a>
                        </dd>
                </dl>


                <!--Genre-->
                <dl class="genres"><dt>Genre:</dt>
                    <dd >
                        <a  v-for='(val, index) in v_content.genre' :href="`${v_dl_path}Genre=${val.genre}`" style="cursor: pointer; text-decoration: none;">{{ val.genre }} {{ v_content.genre.length-1 == index? '' : ', '}}  </a>
                    </dd>
                </dl>

            </div>

            <div class="description">
                <p>{{ v_content.description }}</p>
            </div>

        </div>
        <div class="frame_container" v-show="v_all_frames.length > 0">
            <div class="mediaWrapper">
                <iframe class="frame" :src="v_frame_src"  frameborder="3" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen=""></iframe>
                <div class="server_list">
                    <ul style="list-style-type: none;">
                        <li v-for="(s_el, index) in v_server">
                            <button v-on:click="server(s_el, $event)" :style="[ index==0? v_s_btn_style.select : '']" class="button_server"  >{{ /(\/\/www\.|\/\/)(.*)(?=\.co)/.exec(s_el.url)[2] }}</button>
                        </li>
                    </ul>
                </div>

                <div class="series" v-show="v_all_frames.length > 1">
                    <span>☰ Episode</span>
                    <ul style="list-style-type: none;">
                        <li class="button_series" v-for="(s_el, index) in v_all_frames">
                            <button v-on:click="getFrame(s_el, $event)" :style="[ index==0? v_f_btn_style.select : '']"> {{ s_el[0].name }} </button>
                        </li>
                </ul></div>

            </div>
        </div>

        <div class="trailerWrapper" v-show="v_trailer.show" v-on:click.stop="trailer_diplay()">

            <div class="trailer">
                <span  class="closeMeWrapper"><a v-on:click.stop="trailer_diplay()" class="closeMe trailerClose">❌</a><br> </span>

                <iframe class="trilerFrame" sandbox="allow-forms allow-pointer-lock allow-same-origin allow-scripts allow-top-navigation" :src="v_trailer.src" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen=""></iframe>

            </div>

        </div>

    </div>
</template>

<script>
    import { getFetch } from '../app';
    import { log } from 'util';

    export default {
        data()
            {

                return {

                    v_content   : null,
                    v_favourite : 'far',
                    v_frame_src : null,
                    v_all_frames: [],
                    v_server    : [],
                    v_img       : '',
                    rootUrl     : document.querySelector("meta[name='root_url_f']").getAttribute('content'),
                    v_trailer   : {'show': false, 'src': ''},
                    v_dl_path   : "/project/public/?",
                    v_s_btn_style: { 'select'  :{'background-color': 'rgb(242, 76, 76)'  , 'color':'rgb(241, 247, 241)'},
                                     'unselect':{'background-color': 'rgb(244, 238, 238)', 'color':'black'}
                                    },
                    v_f_btn_style: { 'select'  :{'background-color': 'rgb(122, 198, 127)', 'color':'rgb(241, 247, 241)'},
                                     'unselect':{'background-color': 'rgb(244, 238, 238)', 'color':'black'}
                                     },


                }
            },
        methods: {
            server: function(el, event)
                {
                    let selected_Btn = event.target.textContent;

                    this.v_frame_src   = el.url;

                    Object.assign(event.target.style, this.v_s_btn_style.select );


                    for (let li_el of event.target.parentElement.parentElement.children )
                        {
                            if (li_el.textContent != selected_Btn )
                                {
                                     Object.assign( li_el.children[0].style,  this.v_s_btn_style.unselect );
                                }
                        }
                },
            getFrame: function  ( el, event )
                {
                    let selected_Sery = event.target.textContent;

                    this.v_server    = el;
                    this.v_frame_src = el[0].url;


                    let serie_el = document.querySelectorAll( 'div.series ul>li' );
                    if ( serie_el )
                        {
                             for (let li_el of serie_el )
                                {
                                    li_el.textContent != selected_Sery? Object.assign( li_el.children[0].style,  this.v_f_btn_style.unselect ) :  Object.assign(event.target.style, this.v_f_btn_style.select );
                                }
                        };


                    //---- chenge style of server buttons---
                    let server_el = document.querySelectorAll( 'div.server_list ul>li' );
                    if ( server_el )
                        {
                            for (var [key, value] of server_el.entries())
                                {
                                    key == 0? Object.assign(value.children[0].style, this.v_s_btn_style.select ) : Object.assign( value.children[0].style,  this.v_s_btn_style.unselect );
                                }
                        };




                },
        },
        computed:{
            trailer_diplay: function()
                {
                    return function()
                        {
                            this.v_trailer.show = !this.v_trailer.show;
                            this.v_trailer.src  = this.v_trailer.show && this.v_content.trailer ? this.v_content.trailer : '';
                        }

                },
            user_favorite: function()
                {
                    return async function( ch_status=false )
                        {

                            let local_href   = this.rootUrl;
                            let m_id         = /(?<=\?id=)\d+/.exec(local_href)[0];
                            ch_status        = ch_status ? `&ch_status=true` : '';

                            let url_prepared = local_href.replace(/public\/.*/, `public/ajax/to_favorite?id=${m_id}${ch_status}`)

                            let getF =  await getFetch(url_prepared, "GET", );

                            !getF ? this.v_favourite='far' :  this.v_favourite='fas';

                        }
                },
        },
        async beforeCreate()
            {
                //----[ laravel request to - api/default/getMovie ]---------
                let rootUrl      = document.querySelector("meta[name='root_url_f']").getAttribute('content');
                let url_prepared = rootUrl.replace(/public[/]?/, 'public/ajax/');


                let getM =  await getFetch(url_prepared, "GET", );


                this.v_content = getM;
                this.v_img     = getM.imgU2 || getM.img || 'img/NoImageFound.png' ;

                //---[ sort series ]--
                let v_servers_temp = getM.src ? getM.src : null;
                if( v_servers_temp )
                    {

                        //--- get serie numbers ---
                        let serie_tmp  =  [...new Set( getM.src.map( el=>{ return el.name }) ) ];
                            serie_tmp.sort((f, s)=>{return s- f }).reverse();
                        //---- sort -----
                        for (var serie of serie_tmp)
                            {
                                let tmp_el = v_servers_temp.filter( f_el =>{ return f_el.name == serie } );
                                tmp_el.sort( (a , b)=>
                                    {
                                        if ( /xstreamcdn/.test(a.url) )
                                            return -1;
                                        else if ( /xstreamcdn/.test(b.url) )
                                            return 1;



                                    })
                                tmp_el ? this.v_all_frames.push( tmp_el ) : null;
                            };

                        this.v_all_frames.length > 0 ? this.v_server    = this.v_all_frames[0]         : null;
                        this.v_all_frames.length > 0 ? this.v_frame_src = this.v_all_frames[0][0].url  : null;



                    }

                //---[ check favoutite status ]---
                this.user_favorite(true);

            },
    }
</script>
