<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
        {
        Schema::create('esewas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->string('signature');
            $table->string('transaction_code')->unique();
            $table->string('transaction_uuid');
            $table->string('product_code')->nullable();
            $table->string('price_id');
            $table->string('status');
            $table->string('type');
            $table->timestamp('ends_at')->nullable();
            $table->string('payment_form')->nullable();
            $table->timestamps();
            $table->index(['user_id']);
            });
        }

    /**
     * Reverse the migrations.
     */
    public function down(): void
        {
        Schema::dropIfExists('esewas');
        }
    };
