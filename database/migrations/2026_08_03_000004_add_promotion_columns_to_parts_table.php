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
        Schema::table('parts', function (Blueprint $table) {
            $table->enum('promotion_tier', ['standard', 'premium', 'top', 'vip'])->default('standard')->after('status');
            $table->timestamp('promoted_until')->nullable()->after('promotion_tier');

            $table->index(['status', 'promotion_tier', 'promoted_until']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('parts', function (Blueprint $table) {
            $table->dropIndex(['status', 'promotion_tier', 'promoted_until']);
            $table->dropColumn(['promotion_tier', 'promoted_until']);
        });
    }
};
