<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UserFavourite extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
        {
            //
            Schema::create('userfavourite', function (Blueprint $table)
                {
                    $table->bigIncrements('user_id');
                    $table->integer('idM');
                });
        }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
        {
            //
            Schema::dropIfExists('userfavourite');
        }
}
