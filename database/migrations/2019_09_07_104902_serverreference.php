<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Serverreference extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('serverreference', function (Blueprint $table) {
            //
            $table->bigIncrements('id');
            $table->string('name', 60)->nullable();
            $table->string('url', 350)->nullable()->unique();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('serverreference', function (Blueprint $table) {
            //
        });
    }
}
