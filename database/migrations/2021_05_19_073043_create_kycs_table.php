<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKycsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kycs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('firstName');
            $table->string('lastName');
            $table->string('gender');
            $table->date('dob');
            $table->string('idType');
            $table->string('idNumber')->unique();
            $table->string('language');
            $table->string('address');
            $table->string('phoneNumber')->unique();
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
        Schema::dropIfExists('kycs');
    }
}
