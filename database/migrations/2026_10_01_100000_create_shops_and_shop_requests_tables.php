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
        // 1. Shops table (Moto do'konlar va Ehtiyot qism do'konlari)
        Schema::create('shops', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->enum('type', ['moto', 'parts', 'both'])->default('moto');
            $table->foreignId('city_id')->nullable()->constrained('cities')->nullOnDelete();
            $table->string('address')->nullable();
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();
            $table->string('phone')->nullable();
            $table->string('telegram')->nullable();
            $table->string('instagram')->nullable();
            $table->string('working_hours')->nullable();
            $table->text('description')->nullable();
            $table->string('logo')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('views_count')->default(0);
            $table->timestamps();

            $table->index(['type', 'is_active', 'city_id']);
        });

        // 2. Shop requests table (Do'kon ochish uchun adminga arizalar)
        Schema::create('shop_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('shop_name');
            $table->enum('shop_type', ['moto', 'parts', 'both'])->default('moto');
            $table->foreignId('city_id')->nullable()->constrained('cities')->nullOnDelete();
            $table->string('address')->nullable();
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();
            $table->string('phone');
            $table->text('message')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('admin_notes')->nullable();
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
        });

        // 3. Link listings to shops
        if (Schema::hasTable('listings') && ! Schema::hasColumn('listings', 'shop_id')) {
            Schema::table('listings', function (Blueprint $table) {
                $table->foreignId('shop_id')->nullable()->after('user_id')->constrained('shops')->nullOnDelete();
            });
        }

        // 4. Link parts to shops
        if (Schema::hasTable('parts') && ! Schema::hasColumn('parts', 'shop_id')) {
            Schema::table('parts', function (Blueprint $table) {
                $table->foreignId('shop_id')->nullable()->after('seller_id')->constrained('shops')->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('parts') && Schema::hasColumn('parts', 'shop_id')) {
            Schema::table('parts', function (Blueprint $table) {
                $table->dropForeign(['shop_id']);
                $table->dropColumn('shop_id');
            });
        }

        if (Schema::hasTable('listings') && Schema::hasColumn('listings', 'shop_id')) {
            Schema::table('listings', function (Blueprint $table) {
                $table->dropForeign(['shop_id']);
                $table->dropColumn('shop_id');
            });
        }

        Schema::dropIfExists('shop_requests');
        Schema::dropIfExists('shops');
    }
};
