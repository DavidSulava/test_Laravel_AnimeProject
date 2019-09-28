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
        Schema::create('usersanime_main_has_direcrors', function (Blueprint $table) {
            //
            $table->bigInteger('Anime_main_id')->unsigned()->index();


            $table->foreign('Anime_main_id')
                  ->references('id')->on('anime_main')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');

            $table->bigInteger('director_id')->unsigned()->index();
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
        Schema::dropIfExists('usersanime_main_has_direcrors');
    }
}
