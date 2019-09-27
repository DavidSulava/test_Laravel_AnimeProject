<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AnimeMainHasGenre extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('anime_main_has_genre', function (Blueprint $table) {
            //
            $table->index('Anime_main_id');
            $table->index('genre_id');

            $table->foreign('Anime_main_id')
                  ->references('id')->on('anime_main')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');

            $table->foreign('genre_id')
                  ->references('id')->on('genre')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('anime_main_has_genre');
    }
}
