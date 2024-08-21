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
        Schema::table('jobs', function (Blueprint $table) {
          
            if (Schema::hasColumn('jobs', 'job_location')) {
                $table->dropColumn('job_location');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jobs', function (Blueprint $table) {
            //
            // if (!Schema::hasColumn('jobs', 'job_location')) {
            //   $table->string('job_location')->nullable();
            // }
        });
    }
};
