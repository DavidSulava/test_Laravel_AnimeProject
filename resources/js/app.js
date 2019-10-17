/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

// require('./bootstrap');
import { logo } from './logo.js';


window.Vue = require('vue');
//--vue components
import index_filter    from './components/index_filter';
import movie_content   from './components/movie_content';
import search_bar      from './components/search_bar';
import v_footer        from './components/v_footer';
import index           from './components/index';
import pagination      from './components/pagination';


const v_buss     = new Vue();

document.addEventListener('DOMContentLoaded',async function ()
{
    //----Loggin Button Section/Show-Hide Element----
	// ----------------------------

		if (document.querySelector('.enter'))
        {
            var elem      = document.querySelector('.loginForm');
            var loginBtn  = document.querySelector('.enter')
            var closeElem = document.querySelector('.closeMe')


            hideShow( elem, loginBtn, closeElem)




             //--Button color---
             document.querySelector('.enter').addEventListener('mousedown',function()
                          {
                              document.querySelector('.enter').style = 'background-color: rgb(88, 181, 93);'
                          });
             document.querySelector('.enter').addEventListener('mouseup',function()
                          {
                              document.querySelector('.enter').style = 'background-color: rgb(122, 198, 127);'
                          });



        };


       // ---LOGGED----
       if (document.querySelector('.avatarlogged'))
           {
                 document.querySelector('.avatarlogged').style.cursor = 'pointer';

                 var btn = document.querySelector('.avatarlogged');
                 var elem= document.querySelector('.fold');

                 hideShow(elem, btn)
           };
    //------index Section-------

        let index_Page = document.querySelector( '#m_data_index' );

        if(index_Page)
            {


                // -- index page  Vue instance
                new Vue(
                    {
                        el : '#m_data_index',
                        components:
                            {
                                'v_filter'  : index_filter,
                                'movie_box' : index,
                                'pagination': pagination,
                            },
                    });


            }

        // -- Search bar  Vue instance
        new Vue(
            {
                el : '#search_wrapper',
                components:
                    {
                        'search_bar'  : search_bar,
                    },
            });

        //--hide search results on resize
        window.addEventListener ( 'resize', function ( )
            {
                let results  = document.querySelector( '.search-suggest ul' );

                if(results.style.display == 'block')
                    results.style = 'display:none;'
            } );

        // -- Footer Vue instance
        new Vue(
                {
                    el : 'footer',
                    components:
                        {
                            'v_footer'  : v_footer,
                        },
                });


    //----------get movie Section----------------------

        let id_M =  window.location.search || '' ;

        let content_render_m = document.querySelector( '#content_render_m' );

        if( content_render_m )
            {

                new Vue(
                    {
                        el : '#content_render_m',
                        components:
                            {
                                'movie_content'  : movie_content,
                            },
                    });
            }






    //-----------LOGO---------
      logo ();
})



//----[ Unuversal Functions ] ----//
async function getFetch ( ul, meth=null,  payload=null, searchValue=null, loading=null )
    {
        let inMeth     = meth    ? meth  : "POST";
        let inPayload  = payload ? JSON.stringify(payload) : null;

        ul = searchValue? ul+searchValue : ul;

        var csrf_token  = document.querySelector("meta[name='csrf-token']").getAttribute('content');
        let prs_prevent_token = document.querySelector("meta[name='prs_prevent_token']").getAttribute('content');


        let resp = await fetch( ul,
            {
                method : inMeth,
                body   : inPayload, // data can be `string` or {object}!
                headers: {
                            // 'Content-Type': 'application/x-www-form-urlencoded', // "application/x-www-form-urlencoded"
                            'Content-Type'      : 'application/json',
                            'X-CSRF-TOKEN'      : csrf_token,
                            'prs_prevent_token' : prs_prevent_token
                         }

            }).catch(er=>console.log(er) )

        return  await resp.json().catch(er=>console.log(er) );
    };
function getUrlParams (  params=undefined, get_str=false )
    {
        if(!get_str)
            {
                let dada_str = params ? params : window.location.search;

                let urlParams = new URLSearchParams(dada_str);
                let getObgect = {};

                for ( const [key, val] of urlParams.entries() )
                    {
                        if(key)
                            getObgect[key]=val;
                    }

                return getObgect? getObgect : null
            }
        else
            {
                let rootUrl      = document.querySelector("meta[name='root_url_f']").getAttribute('content');
                let prepared     = rootUrl.replace( /.*(?<=public[/]?)/, '')

                return prepared
            }

    };
function hideShow (  element, btn=undefined, closeElement = undefined, inhtml = undefined  )
    {

        var htmlv;
        var close_html

        document.addEventListener('click', display);

        function display(e)
            {

                !inhtml                           ?  htmlv      = element.outerHTML :  htmlv  = element.innerHTML;
                typeof(closeElement) === 'string' ?  close_html = closeElement      :  closeElement;

                if(!element.clientHeight && e.target === btn )
                        {
                            element.style.cssText = 'display : block ; ';
                        }
                else if( e.target === closeElement)
                        {
                        element.style = 'display : none';
                        }
                else if (close_html && close_html.indexOf(e.target.innerHTML) != -1 )
                        {
                            element.style = 'display : none';
                        }
                else if( htmlv.indexOf(e.target.outerHTML) != -1 && closeElement != 'dont' )
                        {
                            element.style = 'display : block';
                        }
                else
                        {
                            element.style = 'display : none';
                        }



            }

    };
function showHide( area_innerHTML, event, element, closeElement = '&times;' )
    {
        var worcArea = area_innerHTML;
        var targName = event.target.parentNode.innerHTML;
        var index_Of = worcArea.indexOf(targName);

        if(index_Of != -1 && event.target != closeElement)
            element.style = 'display:block;';
        else
            element.style = 'display:none;';

    }


export { getFetch, getUrlParams, v_buss, hideShow,  };