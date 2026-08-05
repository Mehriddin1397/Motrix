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
        Schema::create('motorcycle_specifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('motorcycle_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('engine_type')->nullable();
            $table->unsignedSmallInteger('displacement_cc')->nullable()->index();
            $table->unsignedSmallInteger('horsepower')->nullable()->index();
            $table->unsignedSmallInteger('torque_nm')->nullable();
            $table->unsignedSmallInteger('top_speed_kmh')->nullable();
            $table->unsignedSmallInteger('weight_kg')->nullable();
            $table->decimal('fuel_capacity_l', 5, 1)->nullable();
            $table->decimal('fuel_consumption_l_100km', 4, 1)->nullable();
            $table->string('transmission')->nullable();
            $table->string('cooling_system')->nullable();
            $table->unsignedInteger('price_usd_min')->nullable()->index();
            $table->unsignedInteger('price_usd_max')->nullable()->index();
            $table->decimal('reliability_score', 2, 1)->nullable();
            $table->boolean('beginner_friendly')->default(false)->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('motorcycle_specifications');
    }
};
