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
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->string('media_name');
            $table->string('media_ext');
            $table->string('folder');
            $table->timestamps();
        });

        Schema::create('mediables', function (Blueprint $table) {
            $table->unsignedBigInteger('media_id');
            $table->string('mediables_id');
            $table->string('mediables_type');

            $table->foreign('media_id')->references('id')->on('media')->cascadeOnDelete();
            $table->index(['mediables_id', 'mediables_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media');
        Schema::dropIfExists('mediables');
    }
};
