<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_tokens', function (Blueprint $table) {
            $table->string('hash')->primary();
            $table->text('details');
            $table->string('targetUrl');
            $table->string('afterUrl');
            $table->string('gatewayName');
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
        Schema::drop('payment_tokens');
    }
}
