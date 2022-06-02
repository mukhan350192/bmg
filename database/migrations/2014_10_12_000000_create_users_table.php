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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('iin');
            $table->string('phone');
            $table->string('password');
            $table->string('name');
            $table->string('surname');
            $table->string('fatherName')->nullable();
            $table->string('docNumber');
            $table->string('docIssue');
            $table->string('startGiven');
            $table->string('endGiven');
            $table->string('email');
            $table->integer('leadID');
            $table->string('token');
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
        Schema::dropIfExists('users');
    }
};
