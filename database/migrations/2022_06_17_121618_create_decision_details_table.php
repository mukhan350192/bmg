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
        Schema::create('decision_details', function (Blueprint $table) {
            $table->id();
            $table->integer('leadID');
            $table->integer('amount');
            $table->integer('period');
            $table->integer('reward');
            $table->date('givenDate');
            $table->date('endDate');
            $table->date('lpDate');
            $table->integer('total');
            $table->integer('totalGrace');
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
        Schema::dropIfExists('decision_details');
    }
};
