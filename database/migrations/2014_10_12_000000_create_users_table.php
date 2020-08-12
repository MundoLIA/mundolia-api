<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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

            $table->string('id', 100)->primary();
            $table->string('name');
            $table->string('second_name')->nullable();
            $table->string('last_name');
            $table->string('second_last_name')->nullable();
            $table->string('email')->unique();
            $table->string('id_school')->nullable();
            $table->integer('grade');
            $table->string('avatar');
            $table->string('password');
            $table->boolean('active')->default(false);

            //email
            $table->boolean('verified_email')->default(false);
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
