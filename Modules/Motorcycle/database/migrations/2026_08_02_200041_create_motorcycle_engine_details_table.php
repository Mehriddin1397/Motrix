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
        Schema::create('motorcycle_engine_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('motorcycle_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('animation_url')->nullable();
            $table->longText('working_principle')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('motorcycle_engine_details');
    }
};
