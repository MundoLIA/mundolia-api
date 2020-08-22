<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignkeysToUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->uuid('school_id')->index()->nullable()->after('second_last_name');
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
            $table->string('school_name')->index()->nullable()->after('school_id');
            $table->foreign('school_name')->references('name')->on('schools')->onDelete('cascade');
            $table->unsignedBigInteger('school_key_id')->index()->nullable()->after('school_name');
            $table->foreign('school_key_id')->references('id')->on('school_key_ids')->onDelete('cascade');
            $table->unsignedBigInteger('role_id')->index()->nullable()->after('school_key_id');
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
}
