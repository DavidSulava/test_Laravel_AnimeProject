<?php
return [

    // ---------------Data Base-----------
    'bdname'             => 'anime',
    'tableMaine'         => 'anime_main',
    'movie_and_director' => 'anime_main_has_direcrors',
    'directors'          => 'directors',
    // 'movies_and_artists' => 'movies_and_artists',
    // 'artists'            => 'artists',
    'tableGenre'         => 'genre',
    'movies_genres'      => 'anime_main_has_genre',
    'table_Server'       => 'serverreference',
    'movies_Server'      => 'anime_main_has_serverreference',
    'userfavourite'      => 'userfavourite',
    'users'              => 'users',
    'counter'            => 'counter',


    //-- Data Source
    'update_code'=> env( 'DB_UPDATE_SECRET_CODE' ),
    'dataSource' => env( 'DB_UPDATE_SOURCE' ),


    // CAPCHA
    'capcha' => [
                    'siteKay'   => env( 'SITE_KEY', false ),
                    'secretKey' => env( 'SECRET_KEY', false )
                ],


    // SITE NAME
    'siteName' => 'Anime-HD.com',

];