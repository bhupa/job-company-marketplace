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
        Schema::create('chat_room', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->nullable();
            $table->string('image')->nullable();
             $table->string('payment_type')->nullable(); 
            $table->boolean('status')->default(1);  // status 1 is by default chat is active and 0 is inactive
            $table->boolean('admin_assist')->default(0);
            $table->unsignedBigInteger('matching_id')->nullable();
            $table->foreign('matching_id')->references('id')->on('matching')->onDelete('set null');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->unsignedBigInteger('payment_id')->nullable()->default(null);
            // Add foreign key constraint referencing the 'id' column in the 'payment' table
            $table->foreign('payment_id')
                ->references('id')
                ->on('payments')
                ->onDelete('set null');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->string('type')->nullable(); // type is it direct or match
            $table->unsignedBigInteger('in_app_id')->nullable();
            $table->foreign('in_app_id')->references('id')->on('in_app_purchases')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Schema::dropIfExists('in_app_purchases');
        // Schema::dropIfExists('chat_room');
      
        // Schema::table('chat_room', function (Blueprint $table) {
        //     // Drop the foreign key constraint that references in_app_purchases
        //     $table->dropForeign(['in_app_id']);
        // });
    
        // // Now drop the in_app_purchases table
        // Schema::dropIfExists('in_app_purchases');
        Schema::dropIfExists('chat_room');
       
    }
};
