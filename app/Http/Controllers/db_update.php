<?php

namespace App\Http\Controllers;

use App\Mail\PassReset;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;


set_time_limit(0);
ini_set('memory_limit',-1);


class DB_Update extends Controller
{
    public $code     = 'Sula';

    function __construct(Request $request)
        {
            $this->img_pach = public_path()."\/Movie_IMG/";

            $this->bdname             = config('custom_glob.bdname');
            $this->tableMaine         = config('custom_glob.tableMaine');
            $this->movie_and_director = config('custom_glob.movie_and_director');
            $this->directors          = config('custom_glob.directors');
            $this->tableGenre         = config('custom_glob.tableGenre');
            $this->movies_genres      = config('custom_glob.movies_genres');//$this->movirs_genres
            $this->table_Server       = config('custom_glob.table_Server');
            $this->movies_Server      = config('custom_glob.movies_Server');

        }

    protected function file_contents( $folder, $path)
        {
            $url = preg_replace('/[\s]+/', '%20', $path );
            $str = @file_get_contents($url);
            if ($str === FALSE)
                {
                    // throw new \Exception("Cannot access '$path' to read contents.");
                    echo "Cannot access '$path' to read contents.".PHP_EOL;
                    return '';
                }
            else
                {
                    file_put_contents($folder, $str );
                    return $str;
                }
        }
    protected function server_Table($data, $moviesId)
        {
            $data? $server = $data : $server = null;

            if ($server && count($server) > 0 )
                {
                    for ($j=0; $j < count($server) ; $j++)
                    {

                        if ( isset( $server[$j]) && is_array( $server[$j] ) )
                        {

                            foreach ($server[$j] as $key => $frame)
                                {

                                    if ( is_array($frame) && $frame )
                                    {
                                        foreach ( $frame as $k => $value )
                                        {

                                            if(  is_array($value) && $value )
                                                {
                                                    print_r('==== >>> server array: '.$moviesId);
                                                    foreach ( $value as $el => $url)
                                                        {
                                                            if( $url )
                                                                {

                                                                    $s_id = DB::table($this->table_Server)
                                                                                ->updateOrInsert([ 'url' => $value ],[ 'name' => $key, 'url' => $value ]);

                                                                    $i_Id = DB::table( $this->table_Server )
                                                                                ->where('url' , $value)
                                                                                ->where('name', $key)
                                                                                ->first();

                                                                    if(!$i_Id->id){continue;};

                                                                    DB::table($this->movies_Server)
                                                                        ->updateOrInsert( ['Anime_main_id' => $moviesId, 'serverReference_id' => $i_Id->id ], ['Anime_main_id' => $moviesId,  'serverReference_id' => $i_Id->id ] );
                                                                }
                                                        }

                                                }
                                            else if( !is_array($value) && $value )
                                                {
                                                    DB::table( $this->table_Server )
                                                        ->updateOrInsert( [  'url' => $value  ], [ 'name' => $key, 'url' => $value ] );

                                                    $i_Id = DB::table( $this->table_Server )
                                                                ->where('url' , $value)
                                                                ->where('name', $key)
                                                                ->first();

                                                    if( !isset($i_Id->id) ){continue;};


                                                    DB::table($this->movies_Server)
                                                        ->updateOrInsert( ['Anime_main_id' => $moviesId, 'serverReference_id' => $i_Id->id ], ['Anime_main_id' => $moviesId,  'serverReference_id' => $i_Id->id ] );
                                                }


                                        }
                                    }

                                }
                        }

                    }
                }
            else
                {
                    echo($moviesId.' no data'.PHP_EOL);
                }
        }

    protected function director_Acrtist_ganre($directors_p = null,  $genres = null, $moviesId = null )
        {


            //----Directors Incert in Table------------
                if($directors_p)
                    {
                        for ($j=0; $j < count($directors_p) ; $j++)
                            {
                                $director = trim($directors_p[$j]);

                                DB::table( $this->directors )
                                    ->updateOrInsert( ['name' => $director ] );

                                $i_Id = DB::table( $this->directors )
                                            ->where('name', $director)
                                            ->first();

                                $director_id = isset($i_Id->id) ? $i_Id->id : '';


                                //------movie_and_director Table----
                                if ( $director_id )
                                    {
                                        DB::table( $this->movie_and_director )
                                            ->updateOrInsert( ['Anime_main_id' => $moviesId, 'director_id' => $director_id ] );
                                    }

                            }
                    }

            //----Genres Incert in Table------------
                if($genres)
                    {
                        for ($j=0; $j < count($genres) ; $j++)
                            {

                                $genre = trim($genres[$j]);

                                DB::table( $this->tableGenre )
                                    ->updateOrInsert( ['genre' => $genre ] );

                                $i_Id = DB::table( $this->tableGenre )
                                            ->where('genre', $genre)
                                            ->first();

                                $genreID = isset( $i_Id->id ) ? $i_Id->id : '';

                                //-----movis_and_genres Table------------
                                if ( $genreID  )
                                    {
                                        DB::table( $this->movies_genres )
                                            ->updateOrInsert( ['Anime_main_id' => $moviesId, 'genre_id'=>$genreID ] );

                                    }
                            }
                    }


        }

    protected function get_image($img_url)
        {

            $u_str = md5( uniqid($img_url, true) );

            if( preg_match('~http:\/\/.*.jpg|https:\/\/.*.jpg|http:\/\/.*.png|https:\/\/.*.png~', $img_url) )
                {
                    $tmp  = explode("/", $img_url);
                    $name = end( $tmp );

                    $folder = $this->img_pach.$u_str."_"."$name";

                    // file_put_contents($folder, file_get_contents($img_url));
                    $respIMG = $this->file_contents( $folder,  $img_url );

                    if(!$respIMG)
                        {
                            return $respIMG;
                        }

                    // --get relative path--
                    preg_match_all('~Movie_IMG.*~', $folder , $server_path);

                    return $server_path[0][0];
                }
            else if ( preg_match('~http:\/\/.*|https:\/\/.*~', $img_url) )
                {
                    $tmp    = explode("/", $img_url);
                    $name   = end( $tmp ).'.jpg';

                    $folder = $this->img_pach.$u_str."_"."$name";

                   // file_put_contents($folder, file_get_contents($img_url));
                   $respIMG = $this->file_contents( $folder,  $img_url );

                   if(!$respIMG)
                       {
                           return $respIMG;
                       }

                    preg_match_all('~Movie_IMG.*~', $folder, $server_path);
                    return $server_path[0][0];
                }
            else if ( preg_match ( '#(?![//]+).*#', $img_url ) )
                {
                    preg_match('#(?![//]+).*#', $img_url , $newUrl);

                    $tmp    = explode("/", $img_url);
                    $name   = end( $tmp );

                    $folder = $this->img_pach.$u_str."_"."$name";

                    // file_put_contents($folder, file_get_contents('http://'.$newUrl[0]));
                    $respIMG = $this->file_contents( $folder,  'http://'.$newUrl[0] );

                    if(!$respIMG)
                        {
                            return $respIMG;
                        }

                    preg_match_all('~Movie_IMG.*~', $folder , $server_path);

                    return $server_path[0][0];

                }
            else
                {
                    $tmp    = explode("/", $img_url);
                    $name   = end( $tmp );

                    $folder = $this->img_pach.$u_str."_"."$name";

                   // file_put_contents($folder, file_get_contents($img_url));
                   $respIMG = $this->file_contents( $folder,  $img_url );

                   if(!$respIMG)
                       {
                           return $respIMG;
                       }

                    preg_match_all('~Movie_IMG.*~', $folder , $server_path);
                    return $server_path[0][0];
                }

        }

    public function db_Update( Request $request )
    {
        $get = $request->query();
        $code = isset($get['code']) ? $get['code'] : null;


        // Process
        if($this->code == $code)
        {
            $string = file_get_contents("https://parse-prs.herokuapp.com/body?SulaAnime=get");
            $data   = json_decode($string, true);

            for ($i=0; $i < count($data); $i++)
            {
                if( isset( $data[$i]['title'] ) )
                {
                    isset( $data[$i]['start_year'])   ? $year        = trim($data[$i]['start_year']) : $year        = "";
                    isset( $data[$i]['year'] )        ? $aired       = trim($data[$i]['year'])       : $aired       = '';
                    isset( $data[$i]['episodes'])     ? $episodes    = trim($data[$i]['episodes'])   : $episodes    = '';
                    isset( $data[$i]['media_type'])   ? $type        = trim($data[$i]['media_type']) : $type        = '';
                    isset( $data[$i]['status'] )      ? $status      = trim($data[$i]['status'])     : $status      = '';
                    isset( $data[$i]['infoUrl'] )     ? $infoUrl     = trim($data[$i]['infoUrl'])    : $infoUrl     = '';
                    isset( $data[$i]['description'] ) ? $description = trim($data[$i]['description']): $description = '';
                    isset( $data[$i]['time'] )        ? $time        = trim($data[$i]['time'])       : $time        = '';
                    isset( $data[$i]['rating'] )      ? $rating      = trim($data[$i]['rating'])     : $rating      = '';
                    isset( $data[$i]['country'] )     ? $country     = trim($data[$i]['country'])    : $country     = '';
                    isset( $data[$i]['trailer'] )     ? $trailer     = trim($data[$i]['trailer'])    : $trailer     = '';
                    isset( $data[$i]['img'] )         ? $img         = trim($data[$i]['img'])        : $img         = '';
                    isset( $data[$i]['imgU2'] )       ? $imgU        = trim($data[$i]['imgU2'])      : $imgU        = '';


                    $directors_p = $data[$i]['director'] ? explode(',',$data[$i]['director']) : '';
                    $genres      = explode(',',$data[$i]['genre']);

                    $title       = trim( $data[$i]['title'] );


                    $check  =DB::table( $this->tableMaine  )
                                            ->select('id')
                                            ->where('title', $title )
                                            ->where('start_year', $year )
                                            ->first();


                    //----To Update------
                    if( isset($check->id ) )
                        {

                            $res =  DB::table(  $this->tableMaine  )
                                        ->where([ ['id', '=', $check->id] ])
                                        ->update( [
                                                    'title'  => $title  , 'start_year' => $year    , 'time'      =>$time   ,
                                                    'rating' => $rating , 'country'    => $country , 'trailer'   =>$trailer,
                                                    'imgU2'  => $imgU   , 'aired'      => $aired   , 'media_type'=>$type   ,
                                                    'infoUrl'=> $infoUrl, 'episodes'   => $episodes ,
                                                    'status' => $status , 'description'=> $description,
                                                    ] );


                            $movie  = DB::table( $this->tableMaine  )
                                            ->select('id', 'img')
                                            ->where([ ['id', '=', $check->id] ])
                                            ->first();

                            $image    = isset($movie->img) ? $movie->img : false;


                            // ----- Image Download ----------
                            if(!$image)
                                {

                                    if ($data[$i]['img'])
                                        $image_pach = $this->get_image($data[$i]['img']);
                                    else
                                        $image_pach = '';


                                    DB::table(  $this->tableMaine  )
                                                 ->where([ ['id', '=', $check->id] ])
                                                 ->update( [ 'img'  =>$image_pach ] );
                                }


                            //----Server Incert------------
                            if ( isset( $data[$i]['frame'] )  )
                                {
                                    // Delet All Frames
                                    // DB::table( $this->table_Server )
                                    //     ->select('name', 'url' )
                                    //     ->join( $this->movies_Server, $this->movies_Server.'.serverReference_id', '=', $this->table_Server.'.id'  )
                                    //     ->where( $this->movies_Server.'.Anime_main_id','=', $check->id  )
                                    //     ->delete();
                                    $this->server_Table($data[$i]['frame'], $check->id);
                                }

                            // ----	Director abd artists and Genres-----
                            $this->director_Acrtist_ganre($directors_p,  $genres, $check->id );


                        }
                    else  //----Incert------
                        {
                            if($data[$i]['img'])
                                $patchIMG = $this->get_image($data[$i]['img']);
                            else
                                $patchIMG = '';

                            // mysql_query()
                            $i_id = DB::table( $this->tableMaine )
                                        ->insertGetId([
                                                    'title'  => $title  , 'start_year' => $year    , 'time'      => $time   ,
                                                    'rating' => $rating , 'country'    => $country , 'trailer'   => $trailer,
                                                    'imgU2'  => $imgU   , 'aired'      => $aired   , 'media_type'=> $type   ,
                                                    'infoUrl'=> $infoUrl, 'episodes'   => $episodes, 'img'       => $patchIMG,
                                                    'status' => $status , 'description'=> $description,
                                                ]);



                            $moviesId = $i_id ? $i_id : null;
                            if($moviesId)
                            {
                                //----Server Incert------------
                                if ( isset( $data[$i]['frame'] ) )
                                    {
                                        $this->server_Table($data[$i]['frame'], $moviesId );
                                    }


                                // ----	Director abd artists and Genres-----
                                $this->director_Acrtist_ganre($directors_p, $genres, $moviesId);
                            }
                            else
                            {
                                print_r( $title.' : no ID'.PHP_EOL);

                            }


                        }
                }

            }
        }
        else
        {
            echo 'suspicious request';
        }

        print(PHP_EOL.'-------> All done! <-------');
    }

}