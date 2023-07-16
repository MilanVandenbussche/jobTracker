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
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->boolean('active');
            $table->timestamp('publish_date');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('jobs_langs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('job_id');
            $table->unsignedBigInteger('language_id');
            $table->string('job_title');
            $table->text('job_company');
            $table->text('job_description');
            $table->text('job_qualifications');
            $table->text('job_offer');
            $table->timestamps();

            $table->unique(['job_id', 'language_id']);
            $table->foreign('job_id')->references('id')->on('jobs')->cascadeOnDelete();
            $table->foreign('language_id')->references('id')->on('languages')->cascadeOnDelete();
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
