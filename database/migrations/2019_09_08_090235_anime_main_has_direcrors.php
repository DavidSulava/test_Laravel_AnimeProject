<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AnimeMainHasDirecrors extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('usersanime_main_has_direcrors', function (Blueprint $table) {
            //
            $table->index('Anime_main_id');
            $table->index('director_id');

            $table->foreign('Anime_main_id')
                  ->references('id')->on('anime_main')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');

            $table->foreign('director_id')
                  ->references('id')->on('directors')
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
        Schema::table('usersanime_main_has_direcrors', function (Blueprint $table) {
            //
        });
    }
}
