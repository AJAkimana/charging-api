<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoanAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loan_accounts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('customer_id')->unique();
            $table->float('amount');
            $table->foreign('customer_id')
                ->references('id')->on('customers')
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
        Schema::dropIfExists('loan_accounts');
    }
}
