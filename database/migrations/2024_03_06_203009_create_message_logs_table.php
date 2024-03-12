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
        Schema::create('message_logs', function (Blueprint $table) {
            $table->id();
            $table->string("from");
            $table->string("displayName");
            $table->integer("counter");
            $table->text("messageText");
            $table->boolean("fromMe");
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
        Schema::dropIfExists('message_logs');
    }
};
