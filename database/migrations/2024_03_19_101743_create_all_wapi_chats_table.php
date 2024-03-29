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
        Schema::create('all_wapi_chats', function (Blueprint $table) {
            $table->id();
            $table->string("from");
            $table->text("message");
            $table->string("messageId");
            $table->string("type");
            $table->string("to");
            $table->boolean("fromMe");
            $table->dateTime("messageTime");
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
        Schema::dropIfExists('all_wapi_chats');
    }
};
