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
        Schema::create('new_short_url', function (Blueprint $table) {
            $table->id();
            $table->integer('leadID');
            $table->string('fio');
            $table->string('iin');
            $table->integer('main');
            $table->integer('period');
            $table->integer('amountLast');
            $table->text('address');
            $table->string('cardNumber');
            $table->string('cardGiven');
            $table->string('cardDate');
            $table->string('cardExpiration');
            $table->string('phone');
            $table->string('iban');
            $table->string('email');
            $table->date('givenDate');
            $table->string('repaymentDate');
            $table->integer('repaymentAmount');
            $table->integer('reward');
            $table->string('contractNumber');
            $table->integer('insuranceAmount');
            $table->string('token');
            $table->integer('code');
            $table->string('birthday');
            $table->text('work');
            $table->string('position');
            $table->string('placeOfBirth');
            $table->integer('status');
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
        Schema::dropIfExists('new_short_url');
    }
};
