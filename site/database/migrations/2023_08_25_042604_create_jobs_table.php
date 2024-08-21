<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
        {
        if (Schema::hasTable('jobs')) {
            return;
            }
        Schema::create('jobs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->longText('job_title')->nullable();
            $table->longText('job_title_ja')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->float('salary_from', 10, 2)->nullable();
            $table->float('salary_to', 10, 2)->nullable();
            $table->integer('gender')->nullable();
            $table->unsignedBigInteger('occupation')->nullable();
            $table->foreign('occupation')->references('id')->on('jobs_category')->onDelete('set null');
            $table->integer('experience')->nullable();
            $table->integer('japanese_level')->nullable();
            $table->longText('required_skills')->nullable();
            $table->longText('required_skills_ja')->nullable();
            $table->datetime('published')->nullable();
            $table->datetime('from_when')->nullable();
            $table->boolean('experience_required')->nullable();
            $table->integer('status')->nullable();
            $table->integer('job_type')->nullable();
            $table->string('pay_type')->nullable();
            $table->timestamps();
            });
        }

    /**
     * Reverse the migrations.
     */
    public function down(): void
        {
        Schema::dropIfExists('jobs');
        }
    };