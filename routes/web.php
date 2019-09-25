<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/



Route::get ('/'                     , "PagesControler@index" )->name('home');
Route::get ('/getMovie'             , "PagesControler@getMovie" );
Route::get ('/user_check'           , "PagesControler@user_check" );//--for ajax requests

//-- insert parseed data
Route::get ('/dbUpdate'             , "db_update@db_Update" );


Route::get  ('/registration'          , "CustomAuth@show" );
Route::any  ('/_register'             , 'CustomAuth@register_user');
Route::any  ('/_password/update/email', 'CustomAuth@pass_reset')->name('_password/update/email');

Route::any  ('/emailverify'    , "CustomAuth@email_verify" );

Route::get  ('/profile'        , "CustomAuth@profile" );
Route::post ('/profile_change' , "CustomAuth@edit" );

Auth::routes(['verify' => true]);
//~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

Route::get ('search'                    , "ajaxControler@searchData" )->middleware('csrf_check');
Route::get ('ajax/get_IndexData'        , "ajaxControler@getAllData" )->middleware('csrf_check');
Route::get ('ajax/getMovie'             , "ajaxControler@getMovieData" )->middleware('csrf_check');
Route::get ('ajax/filter'               , "ajaxControler@getFilterData" )->middleware('csrf_check');
Route::get ('ajax/to_favorite'          , "ajaxControler@to_favorite" )->middleware('csrf_check');
Route::get ('ajax/del_movie'            , "ajaxControler@del_movie" )->middleware('csrf_check');
Route::post ('ajax/contactmail'         , "ajaxControler@contactmail" )->middleware('csrf_check');
Route::any ('/avatarChange'             , "ajaxControler@avatarChange" )->middleware('csrf_check');





