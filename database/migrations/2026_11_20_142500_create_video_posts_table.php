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
        Schema::create('video_posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('video_path');
            $table->string('thumbnail_path')->nullable();
            $table->unsignedInteger('duration_seconds')->nullable();
            $table->unsignedBigInteger('views_count')->default(0);

            // Tarmoqlarga yuborish bayroqlari
            $table->boolean('send_to_telegram')->default(true);
            $table->boolean('send_to_youtube')->default(true);
            $table->boolean('send_to_facebook')->default(true);
            $table->boolean('send_to_instagram')->default(true);

            // Yuklanish holatlari: pending, processing, success, failed, skipped
            $table->string('telegram_status')->default('pending');
            $table->string('youtube_status')->default('pending');
            $table->string('facebook_status')->default('pending');
            $table->string('instagram_status')->default('pending');

            // Tarmoqlardagi ID yoki xabarlar
            $table->string('telegram_message_id')->nullable();
            $table->string('youtube_video_id')->nullable();
            $table->string('facebook_post_id')->nullable();
            $table->string('instagram_media_id')->nullable();

            $table->text('error_log')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index(['created_at', 'slug']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('video_posts');
    }
};
