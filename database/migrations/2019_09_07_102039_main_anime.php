<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MainAnime extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('anime_main', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title', 150);
            $table->string('aired', 40)->nullable();
            $table->string('start_year', 10)->nullable();
            $table->string('img', 220 )->nullable();
            $table->string('imgU2', 220)->nullable();
            $table->string('media_type', 10)->nullable();
            $table->string('rating', 5)->nullable();
            $table->string('infoUrl', 180)->nullable();
            $table->string('trailer', 220)->nullable();
            $table->string('time', 10)->nullable();
            $table->string('country', 50)->nullable();
            $table->string('episodes', 5)->nullable();
            $table->string('status', 15)->nullable();
            $table->text('description')->nullable();
            $table->timestamp('dataUpdated')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('anime_main');
    }
}
