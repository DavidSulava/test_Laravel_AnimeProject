<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Genre extends Model
{
    //
    protected $table      = 'genre';
    public    $timestamps = false;

    public function movie()
    {
        return $this->belongsToMany('App\Anime', 'anime_main_has_genre', 'Anime_main_id', 'genre_id');
    }
}
