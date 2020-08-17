<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLicensesKeyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('licenses_key', function (Blueprint $table) {
            $table->string('id', 100)->primary();
            $table->string('license_id')->index();
            $table->foreign('license_id')->references('id')->on('licenses')->onDelete('cascade');
            $table->unsignedBigInteger('user_id')->index();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('licenses_key');
    }
}
