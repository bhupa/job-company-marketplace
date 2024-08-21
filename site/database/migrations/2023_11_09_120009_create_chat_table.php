<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('chat', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->longText('message')->nullable();
            $table->string('file')->nullable();
            $table->datetime('seen')->nullable();
            $table->unsignedBigInteger('chat_room_id')->nullable();
            $table->foreign('chat_room_id')->references('id')->on('chat_room')->onDelete('set null');
            $table->unsignedBigInteger('send_by')->nullable();
            $table->foreign('send_by')->references('id')->on('users')->onDelete('set null');
        
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat');
    }
};
