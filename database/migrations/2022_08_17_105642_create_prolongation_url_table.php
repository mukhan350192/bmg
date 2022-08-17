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
        Schema::create('prolongation_url', function (Blueprint $table) {
            $table->id();
            $table->string('fio')->nullable();
            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('workPlace')->nullable();
            $table->string('position')->nullable();
            $table->string('iin')->nullable();
            $table->string('docNumber')->nullable();
            $table->string('startGiven')->nullable();
            $table->string('endGiven')->nullable();
            $table->string('birthPlace')->nullable();
            $table->string('insurance')->nullable();
            $table->string('prolongationDate')->nullable();
            $table->string('code')->nullable();
            $table->string('period')->nullable();
            $table->string('contractNumber')->nullable();
            $table->string('reward')->nullable();
            $table->string('penalty')->nullable();
            $table->string('amount')->nullable();
            $table->string('token');
            $table->string('status')->default(1);
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
        Schema::dropIfExists('prolongation_url');
    }
};
