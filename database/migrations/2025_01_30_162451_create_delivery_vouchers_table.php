<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeliveryVouchersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delivery_vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('voucher_id')->unique();
            $table->string('ship_no');
            $table->unsignedBigInteger('party_id');
            $table->date('date');
            $table->timestamps();

            $table->foreign('party_id')->references('id')->on('parties')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('delivery_vouchers');
    }
}
