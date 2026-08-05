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
        Schema::create('seller_trust_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->enum('status', ['new', 'trusted', 'restricted'])->default('new');
            $table->unsignedInteger('approved_listings_count')->default(0);
            $table->unsignedInteger('violations_count')->default(0);
            $table->timestamp('last_violation_at')->nullable();
            $table->timestamp('trusted_at')->nullable();
            $table->timestamp('restricted_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seller_trust_profiles');
    }
};
