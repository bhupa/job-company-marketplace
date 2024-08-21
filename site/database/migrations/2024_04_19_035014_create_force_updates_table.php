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
        Schema::create('force_updates', function (Blueprint $table) {
            $table->id();
            $table->string('ios_version')->nullable();
            $table->string('ios_build_number')->nullable();
            $table->string('android_version')->nullable();
            $table->string('android_build_number')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('force_updates');
    }
};
