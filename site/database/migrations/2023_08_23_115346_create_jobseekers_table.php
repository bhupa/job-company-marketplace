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
        Schema::create('jobseekers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->longText('about_ja')->nullable();
            $table->string('profile_img')->nullable();
            $table->datetime('birthday')->nullable();
            $table->string('gender')->nullable(); // male = 1 , female = 2, binary = 3
            $table->string('country')->nullable();
            $table->string('current_country')->nullable();
            $table->unsignedBigInteger('occupation')->nullable();
            $table->foreign('occupation')->references('id')->on('jobs_category')->onDelete('set null');
            $table->string('experience')->nullable(); // less than 1 year = 1, less than 2 year =2, less than 3 year = 3, 3 or more = 4
            $table->string('japanese_level')->nullable(); // N1 = 1 , N2 = 2, N3 = 3, N4 = 4 , N5 =5
            // about should be change to description  for proper meaning
            $table->longText('about')->nullable();
            $table->boolean('employment_status')->nullable();  // 1 parttime, 2 fulltime, 3 ssw, 4 internship
            $table->integer('job_type')->nullable();
            $table->integer('living_japan')->nullable();
            $table->integer('is_verify')->nullable();
            $table->datetime('start_when')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jobseekers');
    }
};
