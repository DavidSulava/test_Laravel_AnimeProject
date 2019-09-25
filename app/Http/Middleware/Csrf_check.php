<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Closure;

class Csrf_check
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
        {

            $SRF        = csrf_token();
            $SRF_token  = !empty($request->header('X-CSRF-TOKEN'))  ? $request->header('X-CSRF-TOKEN')  : NULL;

            preg_match( "/$SRF_token/", $SRF,  $compare_SRF );

            if( !$compare_SRF[0] )
                {
                    abort(404);
                }
            return $next($request);
        }
}
