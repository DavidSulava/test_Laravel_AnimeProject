<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AnimeMainHasServerreference extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('anime_main_has_serverreference', function (Blueprint $table) {
            //
            $table->bigInteger('Anime_main_id')->unsigned()->index();
            $table->foreign('Anime_main_id')
                  ->references('id')->on('anime_main')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');

            $table->bigInteger('serverReference_id')->unsigned()->index();
            $table->foreign('serverReference_id')
                  ->references('id')->on('serverreference')
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
        Schema::dropIfExists('anime_main_has_serverreference');
    }
}
