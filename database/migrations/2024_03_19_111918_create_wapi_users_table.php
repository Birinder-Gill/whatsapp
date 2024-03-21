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
        Schema::create('wapi_users', function (Blueprint $table) {
            $table->id();
            $table->string("chatId");
            $table->string("number")->default("-/-");
            $table->string("isGroup")->default(false);
            $table->string("name");
            $table->text("lastMessage")->nullable();
            $table->boolean("messagesFetched")->default(false);
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
        Schema::dropIfExists('wapi_users');
    }
};
