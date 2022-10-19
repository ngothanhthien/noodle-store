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
        Schema::create('topping', function (Blueprint $table) {
            $table->unsignedBigInteger('main_id');
            $table->unsignedBigInteger('topping_id');
            $table->primary(['main_id','topping_id']);
            $table->foreign('main_id')->references('id')->on('meals');
            $table->foreign('topping_id')->references('id')->on('meals');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('topping');
    }
};
