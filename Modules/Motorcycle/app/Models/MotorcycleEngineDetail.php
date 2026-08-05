<?php

namespace Modules\Motorcycle\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MotorcycleEngineDetail extends Model
{
    use HasFactory;

    protected $fillable = ['motorcycle_id', 'animation_url', 'working_principle'];

    public function motorcycle(): BelongsTo
    {
        return $this->belongsTo(Motorcycle::class);
    }
}
