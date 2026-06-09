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
        Schema::create('artworks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->uuid('reference_set_id');
            $table->string('cdn_url');
            $table->string('storage_path');
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();
            $table->integer('file_size_bytes')->nullable();
            $table->string('mime_type')->nullable();
            $table->text('caption')->nullable();
            $table->string('author_nickname')->nullable();
            $table->string('status')->default('pending');
            $table->integer('likes_count')->default(0);
            $table->uuid('moderated_by')->nullable();
            $table->timestamp('moderated_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('artworks');
    }
};
