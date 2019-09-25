<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Session;
use \App\Http\Middleware\VerifyCsrfToken;

use Auth;
use \App\User;
use \App\Userfavourite;
use \App\Anime;
use \App\Director;
use \App\Genre;
use \App\Serverreference;
use \App\Mail\ContactMe;
use \App\Http\Controllers\Recapcha;


class ajaxControler extends Controller
{

    public function getAllData(Request $request)
        {

            $get          = $request->query();


            $reqOrder_str = "start_year DESC, rating DESC, dataUpdated DESC";
            $default      = ( ( isset($get['Order']) && isset($get['Genre']) ) || isset($get['Country']) || isset($get['Year']) || isset($get['Type'])  ) ? false : true ;

            if( isset($get['f_status']) )
                {
                    return $this->getAll_favorites($request);
                };

            if( $default )
                {
                    if( isset($get['director']) )
                        {

                            $arrDir = Anime::whereHas('director', function($q) use($get)
                                        {
                                            $q->where("name",  $get['director'] );
                                        })
                                        ->select( 'id', 'title',  'media_type', 'episodes', 'aired', 'start_year', 'rating', 'img',  'description', 'imgU2' )
                                        ->orderByRaw( $reqOrder_str )
                                        ->paginate(27)
                                        ->appends($get);

                            return json_encode(  $arrDir );
                        }
                    else if( isset($get['Genre']) )
                        {

                            $arrGenre = Anime::whereHas('genre', function($q) use($get)
                                        {
                                            $q->where("genre",  $get['Genre'] );
                                        })
                                        ->select( 'id', 'title',  'media_type', 'episodes', 'aired', 'start_year', 'rating', 'img',  'description', 'imgU2' )
                                        ->orderByRaw( $reqOrder_str )
                                        ->paginate(27)
                                        ->appends($get);

                            return json_encode(  $arrGenre );
                        }
                    else if( isset($get['title']) )
                        {

                            $affected = Anime::select('id', 'title', 'media_type', 'episodes', 'aired', 'start_year', 'rating', 'img',  'description', 'imgU2' )
                                            ->where( 'title', 'LIKE', '%'.$get['title'].'%' )
                                            ->orderByRaw($reqOrder_str)
                                            ->paginate(27)
                                            ->appends($get);

                            return json_encode(  $affected );
                        }
                    else
                        {
                            $affected = Anime::select('id', 'title',  'media_type', 'episodes', 'aired', 'start_year', 'rating', 'img',  'description', 'imgU2' )
                                               ->orderByRaw($reqOrder_str)
                                               ->paginate(27)
                                               ->appends($get);

                            return json_encode(  $affected );
                        }


                    return json_encode(  '' );
                }
            else
                {
                    $orderBy_str = '';
                    $w_genre     = '';
                    $w_country   = '';
                    $w_year      = '';
                    $where       = [];


                    //------Prepare Request---------------
                    if( isset($get['Order']) )
                        ($get['Order']   == 'Default') ? $orderBy_str.= 'rating DESC, dataUpdated DESC': ( $get['Order'] == 'title' ?   $orderBy_str.="{$get['Order']} ASC"  :  $orderBy_str.="{$get['Order']} DESC");
                    if( isset($get['Country']) )
                        ($get['Country'] == 'All')     ? ""                                            : array_push( $where, [ 'country',  $get['Country'] ] );
                    if( isset($get['Year']) )
                        ($get['Year']    == 'All')     ? $orderBy_str.= ',start_year DESC'             : array_push( $where, [ 'aired', $get['Year'] ] );

                    if( isset($get['Type']) )
                        {
                            if($get['Type']  == 'TV')
                                array_push( $where, [ 'media_type', 'REGEXP', "{$get['Type']}.*|ONA|OVA|Special|^$" ] );
                            else if($get['Type']  == 'Movie')
                                array_push( $where, [ 'media_type', 'REGEXP', 'Movie|OVA|^$' ] );
                        }

                    if( isset($get['Language']) )
                        {
                            if($get['Language'] == 'Subbed')
                                array_push( $where, [ 'title', 'NOT REGEXP',  '\(Dub\)$|[\s]+Dub$' ] );
                            elseif($get['Language'] == 'Dubbed')
                                array_push( $where, [ 'title', 'REGEXP',  '\(Dub\)$|[\s]+Dub$' ] );
                        }

                    //-------Make Request------------------

                    if( isset( $get['Genre'] ) && $get['Genre'] != 'All')
                        {

                            $arrGenre    = Anime::whereHas('genre', function($q) use($get)
                                                    {
                                                        $q->where("genre",  $get['Genre'] );
                                                    })
                                                ->select( 'id', 'title',  'media_type', 'episodes', 'aired', 'start_year', 'rating', 'img',  'description', 'imgU2' )
                                                ->orderByRaw( $orderBy_str )
                                                ->paginate(27)
                                                ->appends($get);


                            return json_encode(  $arrGenre );

                        }
                    else
                        {
                            if(!$orderBy_str)
                                $orderBy_str = 'rating DESC, dataUpdated DESC';

                            $affected = Anime::select('id', 'title', 'media_type', 'episodes', 'aired', 'start_year', 'rating', 'img',  'description', 'imgU2' )
                                                ->where($where)
                                                ->orderByRaw($orderBy_str)
                                                ->paginate(27)
                                                ->appends($get);

                            return json_encode(  $affected );
                        }

                }


        }
    public function getMovieData(Request $request)
        {

            $get      = $request->query();
            $image    = config('custom_glob.imgSwitsh');// imgU or image
            $usertype = isset( session('user')->userType ) ? session('user')->userType : NULL;
            $page_id  = isset( $get['id'] ) ? intval($get['id']) : NULL;

            if($page_id)
                {
                    $arrMovie = Anime::select('id', 'title', 'media_type', 'episodes', 'trailer', 'aired', 'start_year', 'rating', 'img',  'description', 'imgU2', 'time', 'status' )
                                        ->where('id', $page_id )
                                        ->first();

                    $arrServer   = Anime::find( $page_id )->serverreference()->get();
                    $arrGenre    = Anime::find( $page_id )->genre()->get();
                    $arrDirector = Anime::find( $page_id )->director()->get();

                    $arrMovie ? $arrMovie->src      = $arrServer   && count($arrServer )   > 0? $arrServer   : ''    : '' ;
                    $arrMovie ? $arrMovie->genre    = $arrGenre    && count($arrGenre )    > 0? $arrGenre    : ''    : '' ;
                    $arrMovie ? $arrMovie->director = $arrDirector && count($arrDirector ) > 0? $arrDirector : ''    : '' ;
                }
            else
                {
                    $arrMovie='';
                }



            return json_encode(   $arrMovie  );

        }
    public function getFilterData(Request $request)
        {
            $arrData = [];
            $get     = $request->query();

            if( isset( $get['filterCategories'] ) )
            {

                $g_resp = Genre::select('genre')
                                 ->get();
                $arrData['genres']=  $g_resp;

                $g_resp = Anime::select('country')
                                ->distinct()
                                ->get();
                $arrData['countries']=  $g_resp;

                $g_resp = Anime::select('start_year')
                                ->distinct()
                                ->get();
                $arrData['years']=  $g_resp;
            }


            return json_encode(   $arrData  );
        }
    public function searchData(Request $request)
        {
            $get          = $request->query();
            $reqOrder_str = "start_year DESC, rating DESC";

            if( isset($get['title']) )
                {

                    $affected =Anime::select('id', 'title',  'media_type', 'episodes', 'aired', 'start_year', 'img', 'imgU2' )
                                    ->where('title', 'LIKE', '%'.$get['title'].'%')
                                    ->distinct()
                                    ->limit(5)
                                    ->get();

                    return count($affected)>0 ? json_encode(  $affected ): json_encode( '' );

                }
            return json_encode( '' );
        }

    public function to_favorite(Request $request)
        {
            if( auth()->check() && isset($request->id) && !isset( $request->ch_status ) )
                {
                    //--[ add or delete from favourutes ]--

                    $fav_record = User::find( auth()->user()->id )->favorite->where( 'idM', $request->id );

                    if( count( $fav_record ) > 0 )
                        {

                            $del = Userfavourite::where( 'user_id', auth()->user()->id )
                                                  ->where( 'idM', $request->id )
                                                  ->delete();
                            return json_encode(  false  );
                        }
                    else
                        {

                            $user_fav = new Userfavourite;

                            $user_fav->user_id = auth()->user()->id;
                            $user_fav->idM     = $request->id;

                            $user_fav->save();


                            return json_encode(  true  );
                        }

                }
            else if( auth()->check() && isset($request->id) && isset( $request->ch_status ) )
                {
                    //---[ check favoutite status ]---
                    //--------------------------------

                    $fav_record = User::find( auth()->user()->id )->favorite->where( 'idM', $request->id );

                    if( count( $fav_record ) > 0 )
                        {
                            return json_encode(  true  );
                        }
                    else
                        {
                            return json_encode(  false  );
                        }
                }

            return abort(404) ;
        }
    public function del_movie(Request $request)
        {

            if( auth()->check() && auth()->user()->userType == 'Admin'  && isset($request->id) && !isset( $request->ch_status ) )
                {

                    $m_record = $request->id;

                    $del = Anime::find( $m_record )->serverreference()->delete();
                    Anime::destroy($m_record);


                    return json_encode(  true  );


                }
            return abort(404) ;
        }
    public function getAll_favorites(Request $request)
        {

            if( auth()->check() )
                {

                    $fav_record = User::find( auth()->user()->id )->favorite->pluck('idM');

                    if( count( $fav_record ) > 0 )
                        {
                            $get          = $request->query();

                            $reqOrder_def = "rating DESC, dataUpdated DESC";
                            $where        = [];
                            $orderBy_str  = '';


                            $getFavs = Anime::select( 'id', 'title',  'media_type', 'episodes', 'aired', 'start_year', 'rating', 'img',  'description',  'imgU2' )
                                                ->whereIn( 'id', $fav_record )
                                                ->where($where)
                                                ->orderByRaw($reqOrder_def)
                                                ->paginate(27)
                                                ->appends( $get );


                            return json_encode(  $getFavs  );
                        }

                }

            return null;
        }
    public function contactmail( Request $request )
        {

            $response = new Recapcha( $request );

            if( !$response->check() )
                {
                    return json_encode(  ['erCaptcha' => 'Check the box above  if you are not a robot'] );
                }
            else
                {
                    if( isset( $request['email'] ) && isset( $request['message'] ) )
                        {

                            $dSend = [
                                        'mail'=> $request['email'] ,
                                        'msg' => $request['message'],
                                        'host'=> env('APP_NAME')
                                    ];

                            $c_mail= Mail::send( new ContactMe( $dSend ) );

                            if ( !Mail::failures() )
                                {
                                    //--email has ben sent

                                    return json_encode ( 'true' );
                                }
                            else
                                {
                                    //echo $mailer->ErrorInfo;
                                    return json_encode ( 'false' );
                                }

                        }
                }



            return abort(404);
        }

    public function avatarChange(Request $request)
        {
            $input = $request->all();


            $user = User::find(Auth::user()->id);

            if (isset($input['submitAvatar']) || isset($input['cropAvatar']) &&  $user )
                {
                    function testUrl( $data, $sImgPath )
                    {
                        $imgDir   = base_path().'/public/img/Avatars/';
                        $img_Name = md5(microtime().uniqid()).'.'.'jpeg';
                        $loadLinl = $imgDir.$img_Name;
                        $workLink = $imgDir.$img_Name;



                        file_put_contents($loadLinl, base64_decode($data));

                        $obj_user          = Auth::user();
                        $prev_avatar       = $obj_user->avatar; //previus avatar
                        $obj_user->avatar  =  'img/Avatars/'.$img_Name ; //desired avatar

                        $obj_user->save();
                        $obj_user->refresh();

                        // Delete Old img
                        if( $prev_avatar )
                            {
                                try
                                    {
                                        unlink( base_path().'\public\/'.$sImgPath);
                                    }
                                catch( Exception $e )
                                    {

                                    }
                            }

                        return response()->json(array('img'=> 'The image succesfully added!'), 200);


                    };

                    if(isset($input['submitAvatar']))
                    {
                        $tempUrl   = base64_encode(file_get_contents($input['submitAvatar']));
                        return  $tempUrl;

                    }

                    if( isset($input['cropAvatar']) )
                    {

                        $cropURL  = $input['cropAvatar'];
                        $cropURL  = str_replace(' ','+', $cropURL);


                        $sImgPath = $input['sImgPath'];

                        return testUrl( $cropURL, $sImgPath );
                    }

                }




            return response()->json(array('msg'=> 'true'), 200);
        }

}