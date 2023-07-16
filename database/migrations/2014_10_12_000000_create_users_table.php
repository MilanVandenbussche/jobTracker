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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('password');
            $table->boolean('admin');
            $table->unsignedBigInteger('language_id');
            $table->string('email')->unique();
            $table->unsignedBigInteger('media_id')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('media_id')->references('id')->on('media');
            $table->foreign('language_id')->references('id')->on('languages');
        });

        Schema::create('favorites', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('parent_id');
            $table->unsignedBigInteger('child_id');
            $table->timestamp('created_at');

            $table->unique(['parent_id', 'child_id']);
            $table->foreign('parent_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('child_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('user_favorites');
        Schema::dropIfExists('favorites');
    }
};
