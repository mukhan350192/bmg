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
        Schema::create('user_work', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('sphere');
            $table->text('workPlace')->nullable();
            $table->string('position');
            $table->double('lastSix');
            $table->double('deposit');
            $table->string('fioContact');
            $table->string('phoneContact');
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
        Schema::dropIfExists('user_work');
    }
};
