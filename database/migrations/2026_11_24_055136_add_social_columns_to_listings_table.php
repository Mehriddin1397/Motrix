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
        Schema::table('listings', function (Blueprint $table) {
            $table->string('telegram_channel_message_id')->nullable()->after('views_count');
            $table->string('telegram_group_message_id')->nullable()->after('telegram_channel_message_id');
            $table->string('instagram_media_id')->nullable()->after('telegram_group_message_id');
            $table->string('telegram_status')->default('idle')->after('instagram_media_id');
            $table->string('instagram_status')->default('idle')->after('telegram_status');
            $table->timestamp('social_published_at')->nullable()->after('instagram_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->dropColumn([
                'telegram_channel_message_id',
                'telegram_group_message_id',
                'instagram_media_id',
                'telegram_status',
                'instagram_status',
                'social_published_at',
            ]);
        });
    }
};
