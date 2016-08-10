<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payum_payments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('details');
            $table->string('number');
            $table->string('description');
            $table->string('clientId');
            $table->string('clientEmail');
            $table->string('totalAmount');
            $table->string('currencyCode');
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
        Schema::drop('payum_payments');
    }
}
