<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned()->index();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->bigInteger('visitor_id')->unsigned()->index();
            $table->foreign('visitor_id')->references('id')->on('visitors')->onDelete('cascade');

            $table->string('invoice_number');

            $table->string('external_id');
            $table->bigInteger('total');
            $table->bigInteger('grand_total');

            $table->string('payment_link')->nullable();
            $table->string('status');

            $table->tinyInteger('has_withdrawn');
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
        Schema::dropIfExists('payments');
    }
};
