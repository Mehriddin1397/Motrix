<?php

namespace Modules\Motorcycle\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MotorcycleSpecification extends Model
{
    use HasFactory;

    protected $fillable = [
        'motorcycle_id',
        'powertrain_type',
        'engine_type',
        'displacement_cc',
        'horsepower',
        'torque_nm',
        'top_speed_kmh',
        'weight_kg',
        'fuel_capacity_l',
        'fuel_consumption_l_100km',
        'transmission',
        'cooling_system',
        'battery_capacity_kwh',
        'range_km',
        'charging_time_hours',
        'fast_charging_time_min',
        'price_usd_min',
        'price_usd_max',
        'reliability_score',
        'beginner_friendly',
    ];

    protected function casts(): array
    {
        return [
            'beginner_friendly' => 'boolean',
            'reliability_score' => 'decimal:1',
            'fuel_capacity_l' => 'decimal:1',
            'fuel_consumption_l_100km' => 'decimal:1',
            'battery_capacity_kwh' => 'decimal:2',
            'range_km' => 'integer',
            'charging_time_hours' => 'decimal:1',
            'fast_charging_time_min' => 'integer',
        ];
    }

    public function isElectric(): bool
    {
        return $this->powertrain_type === 'electric';
    }

    public function isPetrol(): bool
    {
        return $this->powertrain_type !== 'electric';
    }

    public function motorcycle(): BelongsTo
    {
        return $this->belongsTo(Motorcycle::class);
    }
}
