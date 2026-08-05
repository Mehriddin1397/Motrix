<?php

namespace Modules\Comparison\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Modules\Motorcycle\Models\Motorcycle;

class Comparison extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'session_token'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function motorcycles(): BelongsToMany
    {
        return $this->belongsToMany(Motorcycle::class, 'comparison_items');
    }
}
