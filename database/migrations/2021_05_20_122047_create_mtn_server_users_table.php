<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMtnServerUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mtn_server_users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('names');
            $table->string('phoneNumber')->unique();
            $table->string('location');
            $table->date('dateOfBirth');
            $table->string('kyc')->unique();
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
        Schema::dropIfExists('mtn_server_users');
    }
}
