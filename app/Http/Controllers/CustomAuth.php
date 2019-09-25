<?php

namespace App\Http\Controllers;


use App\Users;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

use App\Http\Controllers\Recapcha;
use App\Http\Controllers\Auth\RegisterController;
use Auth;
use App\Http\Controllers\Auth\ForgotPasswordController;


class CustomAuth extends Controller
{



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register_user( Request $request, RegisterController $register, Recapcha $recapCHA)
        {
            $response = new Recapcha( $request );

            if( !$response->check() )
                {
                    return back()->with('erCaptcha', 'Check the box below  if you are not a robot')->withInput();
                }
            else
                {
                    $reg  = new $register;
                    $reg->register($request);
                }
        }

    /**
     * Display the specified resource.
     *
     * @param  \App\Anime $anime
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
        {
            //- if logged return to main page
            if ( Auth::check() )
                {
                    return redirect()->intended();
                }

            // else proceed
            return view("registration");
        }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Anime $anime
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
        {
            if( $request->has('submit_profile_change') )
                {
                    $request->validate(['email'    => ['required', 'string', 'email'  , 'max:255', Rule::unique('users')->ignore(Auth::user()->id) ],
                                        'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore(Auth::user()->id) ],
                                        'firstName'=> [ 'max:255'],
                                        'lastName' => [ 'max:255'],
                                        'phone'    => [ 'max:20'],
                                        ]);

                    $obj_user = Auth::user();
                    $obj_user->username  =  $request['username'];
                    $obj_user->email     =  $request['email'];
                    $obj_user->firstName =  $request['firstName'];

                    $obj_user->save();
                    $obj_user->refresh();

                    return redirect()->back()->with("success","Data changed successfully !");
                }

            if( $request->has('submitPassChange') )
                {
                    $request->validate(['password' => ['required', 'confirmed', 'min:8'] ]);

                    $obj_user = Auth::user();

                    $obj_user->password = Hash::make($request->password);

                    $obj_user->save();
                    $obj_user->refresh();

                    return redirect()->back()->with("pas_success","Password changed successfully!");
                }

            if( $request->has('submitDelete') )
                {
                    $request->validate(['email_del'    => ['required', 'string', 'email', 'max:255' ],
                                        'password_del' => ['required', 'min:8'] ],
                                        [
                                            'email_del.required'    => 'The email field is required!',
                                            'email_del.email'       => 'The email must be a valid email address',
                                            'password_del.required' => 'The password field is required!',
                                            'password_del.min'      => 'The password must be at least 8 characters.'
                                        ]);


                    $status = $this->destroy();

                    if($status)
                        {
                            return redirect()->route('home')->with('Ac_deleted', 'Your account has been deleted!');
                        }
                    return redirect()->back();
                }

        }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Anime $anime
     * @return \Illuminate\Http\Response
     */
    public function destroy()
        {
            //--Delete an User
            $user = User::find(Auth::user()->id);


            if($user)
                {
                    Auth::logout();

                    if ( $user->delete() )
                        {
                            return true;
                        }
                }
            return false;

        }

    public function email_verify()
        {
            return view("auth/verify");
        }

    public function   profile()
        {
            return view("auth/profile");
        }
    public function   pass_reset( Request $request )
        {
            $response = new Recapcha( $request );

            if( !$response->check() )
                {
                    return back()->with('erCaptcha', 'Check the box below  if you are not a robot')->withInput();
                }
            else
                {

                    $r_passAuth = new ForgotPasswordController($request);
                    $r_passAuth->sendResetLinkEmail($request);

                    return back()->withInput();
                }
        }

}
