<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLicensesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('licenses', function (Blueprint $table) {

            $table->string('id', 100)->primary();
            $table->string('titular');
            $table->string('email_admin')->unique();
            //foreign keys
            $table->string('school_id')->index();
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->unsignedBigInteger('license_type_id')->index();
            $table->foreign('license_type_id')->references('id')->on('licenses_type')->onDelete('cascade');
            $table->unsignedBigInteger('user_id')->index();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            //extra info
            $table->integer('studens_limit');
            $table->dateTime('purchase_at', 0);
            $table->boolean('is_current');
            $table->dateTime('expiration_date', 0);
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
        Schema::dropIfExists('licenses');
    }
}
