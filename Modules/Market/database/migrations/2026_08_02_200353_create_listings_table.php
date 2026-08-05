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
        Schema::create('listings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('motorcycle_id')->nullable()->constrained('motorcycles')->nullOnDelete();
            $table->foreignId('brand_id')->nullable()->constrained('brands')->nullOnDelete();
            $table->string('custom_title')->nullable();
            $table->year('year');
            $table->decimal('price', 12, 2);
            $table->enum('currency', ['USD', 'UZS'])->default('USD');
            $table->unsignedInteger('mileage_km')->default(0);
            $table->enum('condition', ['new', 'used']);
            $table->foreignId('city_id')->constrained();
            $table->text('description');
            $table->enum('status', ['pending', 'active', 'sold', 'rejected', 'expired'])->default('pending');
            $table->boolean('is_featured')->default(false);
            $table->unsignedInteger('views_count')->default(0);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'brand_id', 'city_id', 'price']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('listings');
    }
};
