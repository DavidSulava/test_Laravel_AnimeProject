<?php

namespace App;

use Illuminate\Database\Eloquent\Model;



class Anime extends Model
    {
        protected $table = 'anime_main';

        public function serverreference()
            {
                return $this->belongsToMany('App\Serverreference', 'anime_main_has_serverreference', 'Anime_main_id', 'serverReference_id');
            }
        public function genre()
            {
                return $this->belongsToMany('App\Genre', 'anime_main_has_genre', 'Anime_main_id', 'genre_id');
            }
        public function director()
            {
                return $this->belongsToMany('App\Director', 'anime_main_has_direcrors', 'Anime_main_id', 'director_id');
            }
    }


