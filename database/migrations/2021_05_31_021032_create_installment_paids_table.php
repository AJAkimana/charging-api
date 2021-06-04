<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInstallmentPaidsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('installment_paids', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->float('amount');
            $table->float('amount_not_paid');
            $table->float('added_amount');
            $table->uuid('customer_id');
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
        Schema::dropIfExists('installment_paids');
    }
}
