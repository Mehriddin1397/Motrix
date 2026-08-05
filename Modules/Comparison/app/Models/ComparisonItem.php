<?php

namespace Modules\Comparison\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Motorcycle\Models\Motorcycle;

class ComparisonItem extends Model
{
    use HasFactory;

    protected $fillable = ['comparison_id', 'motorcycle_id'];

    public function comparison(): BelongsTo
    {
        return $this->belongsTo(Comparison::class);
    }

    public function motorcycle(): BelongsTo
    {
        return $this->belongsTo(Motorcycle::class);
    }
}
