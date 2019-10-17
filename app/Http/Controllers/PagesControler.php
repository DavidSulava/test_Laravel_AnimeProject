<?php

namespace App\Http\Controllers;

use App\Mail\PassReset;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;


// require_once base_path().'/public/db_Handle/dataBaseEnter.php';



class PagesControler extends Controller
{

    public function index( Request $request )
        {

            //--Test feature to prevent data parsing
            $prs_prevent_token = hash('sha256', Str::random(60) );

            isset($request->session('prs_prevent_token')->default) ? TRUE : session( ['prs_prevent_token' => ['default' => $prs_prevent_token ] ] );

            return view( "index", compact( 'prs_prevent_token' ) );
        }

    public function getMovie( Request $request )
        {
            return view("getMovie" );
        }
    public function user_check( Request $request )
        {
            $c_user = auth()->check() ?  [ 'auth'=> 1,'u_type' => auth()->user()->userType ] : '' ;

            return json_encode( $c_user ) ;
        }

}

