<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
        {
            Schema::create('users', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('username', 255)->unique();
                $table->string('email')->unique();
                $table->string('avatar', 250)->nullable();
                $table->string('userType')->nullable();
                $table->string('firstName', 255)->nullable();
                $table->string('lastName', 255)->nullable();
                $table->string('phone', 20)->nullable();
                $table->string('password');
                $table->timestamp('email_verified_at')->nullable();
                $table->rememberToken();
                $table->timestamps();
            });
        }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
        {
            Schema::dropIfExists('users');
        }
}
