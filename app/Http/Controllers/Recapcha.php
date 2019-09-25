<?php

namespace App\Http\Controllers;

use App\Mail\PassReset;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;


// require_once base_path().'/public/db_Handle/dataBaseEnter.php';



class Recapcha extends Controller
{
    public $recapCHA ;

    function __construct(Request $request)
        {
            $this->recapCHA = function() use ($request)
                {

                    //--Google reCAPTCHA
                    $siteKay     =  env('SITE_KEY');
                    $secretKey   =  env('SECRET_KEY');
                    $responseKay = !isset( $request['g-recaptcha-response'] ) ? '': $request['g-recaptcha-response'];
                    $userIP      = request()->ip();
                    $url         = "https://www.google.com/recaptcha/api/siteverify?secret=$secretKey&response=$responseKay&remoteip=$userIP";

                    $response    = file_get_contents($url);
                    $response    = json_decode($response);

                    return $response;

                };
        }

    public function check()
    {

        $response = ($this->recapCHA)();

        if($response->success == 1 )
            {
                return true;
            }
        else
            {
                return false;
            }
    }

}

