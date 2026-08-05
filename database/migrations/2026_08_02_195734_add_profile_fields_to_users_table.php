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
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->unique()->nullable()->after('name');
            $table->string('phone')->unique()->nullable()->after('email');
            $table->timestamp('phone_verified_at')->nullable()->after('phone');
            $table->string('avatar')->nullable()->after('phone_verified_at');
            $table->text('bio')->nullable()->after('avatar');
            $table->foreignId('city_id')->nullable()->after('bio')->constrained()->nullOnDelete();
            $table->enum('experience_level', ['beginner', 'intermediate', 'expert'])->nullable()->after('city_id');
            $table->unsignedSmallInteger('height_cm')->nullable()->after('experience_level');
            $table->unsignedInteger('budget_usd')->nullable()->after('height_cm');
            $table->enum('status', ['active', 'banned', 'pending'])->default('active')->after('budget_usd');
            $table->timestamp('last_active_at')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('city_id');
            $table->dropColumn([
                'username', 'phone', 'phone_verified_at', 'avatar', 'bio',
                'experience_level', 'height_cm', 'budget_usd', 'status', 'last_active_at',
            ]);
        });
    }
};
