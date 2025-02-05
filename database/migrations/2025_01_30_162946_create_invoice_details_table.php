<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoiceDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoice_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('delivery_voucher_id'); // Foreign Key
            $table->integer('pcs');
            $table->decimal('weight', 10, 2);
            $table->decimal('rate', 10, 2);
            $table->decimal('amt_clring', 10, 2);
            $table->decimal('duty', 10, 2);
            $table->decimal('total', 10, 2);
            $table->timestamps();

            $table->foreign('delivery_voucher_id')->references('id')->on('delivery_vouchers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoice_details');
    }
}
