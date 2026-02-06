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
        Schema::create('scheduled_posts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('video_id')->constrained('videos')->cascadeOnDelete();
            $table->string('platform');
            $table->dateTime('publish_at');
            $table->text('caption')->nullable();
            $table->text('hashtags')->nullable();
            $table->enum('status', ['pending', 'publishing', 'published', 'failed'])->default('pending');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scheduled_posts');
    }
};
