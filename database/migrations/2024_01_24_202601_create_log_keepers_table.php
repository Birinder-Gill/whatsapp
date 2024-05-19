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
        Schema::create('log_keepers', function (Blueprint $table) {
            $table->id();
            $table->string('to');
            $table->integer('price')->default(0);
            $table->integer('address')->default(0);
            $table->integer('moreDetails')->default(0);
            $table->integer('useCase')->default(0);
            $table->integer('deliveryWay')->default(0);
            $table->integer('deliveryWime')->default(0);
            $table->integer('pincodeAvailability')->default(0);
            $table->integer('followUpGivenByUser')->default(0);
            $table->integer('ok')->default(0);
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
        Schema::dropIfExists('log_keepers');
    }
};
