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
        Schema::table('motorcycle_specifications', function (Blueprint $table) {
            $table->string('powertrain_type')->default('petrol')->after('motorcycle_id')->index();
            $table->decimal('battery_capacity_kwh', 6, 2)->nullable()->after('cooling_system');
            $table->unsignedSmallInteger('range_km')->nullable()->after('battery_capacity_kwh')->index();
            $table->decimal('charging_time_hours', 4, 1)->nullable()->after('range_km');
            $table->unsignedSmallInteger('fast_charging_time_min')->nullable()->after('charging_time_hours');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('motorcycle_specifications', function (Blueprint $table) {
            $table->dropColumn([
                'powertrain_type',
                'battery_capacity_kwh',
                'range_km',
                'charging_time_hours',
                'fast_charging_time_min',
            ]);
        });
    }
};
