<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('kyc_id')->unique();
            $table->uuid('offer_id')->nullable();
            $table->uuid('next_of_kin_id')->nullable();
            $table->string('msisdn')->unique();
            $table->string('location');
            $table->string('customer_status')->default('pre_customer');
            $table->string('picture')->nullable();
            $table->string('application_form')->nullable();
            $table->string('loan_status')->default('pending');
            $table->date('monthversary_date')->nullable();
            $table->string('installment_period')->nullable();
            $table->float('installment_amount')->nullable();
            $table->foreign('kyc_id')
                ->references('id')->on('kycs')
                ->onDelete('cascade');
            $table->foreign('offer_id')
                ->references('id')->on('offers')
                ->onDelete('cascade');
            $table->foreign('next_of_kin_id')
                ->references('id')->on('next_of_keens')
                ->onDelete('cascade');
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
        Schema::dropIfExists('customers');
    }
}
