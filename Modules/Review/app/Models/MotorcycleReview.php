<?php

namespace Modules\Review\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Motorcycle\Models\Motorcycle;

class MotorcycleReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'motorcycle_id',
        'user_id',
        'rating',
        'ownership_period',
        'pros',
        'cons',
        'body',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
        ];
    }

    public function motorcycle(): BelongsTo
    {
        return $this->belongsTo(Motorcycle::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
