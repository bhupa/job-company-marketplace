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
        Schema::create('pricings', function (Blueprint $table) {
            $table->id();
            $table->string("key")->unique(); //eg: jobseeker_one_week
            $table->string("ios"); //eg: jobseeker_one_week
            $table->string("android"); //eg: jobseeker_one_week
            $table->string('pricing_type'); //subscription/superchat
            $table->integer('user_type')->nullable(); // user_type 1 = jobseeker, 2 = company
            $table->json('name');
            $table->json('prices');
            $table->json('time_period');
            $table->json('features')->nullable();
            $table->integer('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pricings');
    }
};
