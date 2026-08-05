<?php

namespace Modules\Motorcycle\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MotorcycleProCon extends Model
{
    use HasFactory;

    protected $table = 'motorcycle_pros_cons';

    protected $fillable = ['motorcycle_id', 'type', 'text'];

    public function motorcycle(): BelongsTo
    {
        return $this->belongsTo(Motorcycle::class);
    }
}
