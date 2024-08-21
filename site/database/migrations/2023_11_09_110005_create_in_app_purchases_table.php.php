<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // if (Schema::hasTable('in_app_purchases')) {
        //     return;
        // }
        Schema::create('in_app_purchases', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('item_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->string('status');
            $table->longText('purchase_token')->nullable();
            $table->string('currency')->nullable();
            $table->longText('store_user_id')->nullable();
            $table->longText('order_id')->nullable();
            $table->decimal('price', 20)->nullable();
            $table->string('payment_type')->nullable(); //'subscription, chat or any other thing '
            $table->string('payment_for')->nullable(); //'google' | 'apple'
            $table->longText('transaction_receipt')->nullable(); 
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('in_app_purchases');
    }
};
