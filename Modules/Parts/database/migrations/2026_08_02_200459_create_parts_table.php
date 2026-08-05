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
        Schema::create('parts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('category_id')->constrained('part_categories')->restrictOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->enum('part_type', ['oem', 'aftermarket']);
            $table->string('part_number')->nullable();
            $table->decimal('price', 12, 2);
            $table->unsignedInteger('stock_qty')->default(0);
            $table->enum('condition', ['new', 'used']);
            $table->text('description');
            $table->enum('status', ['pending', 'active', 'sold_out', 'rejected'])->default('pending');
            $table->timestamps();

            $table->index(['status', 'category_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parts');
    }
};
