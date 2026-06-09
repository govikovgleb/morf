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
        Schema::create('reference_images', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('category_id');
            $table->string('cdn_url');
            $table->string('storage_path');
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();
            $table->integer('file_size_bytes')->nullable();
            $table->string('mime_type')->nullable();
            $table->uuid('uploaded_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reference_images');
    }
};
