<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SellerTrustProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'status',
        'approved_listings_count',
        'violations_count',
        'last_violation_at',
        'trusted_at',
        'restricted_at',
    ];

    protected function casts(): array
    {
        return [
            'last_violation_at' => 'datetime',
            'trusted_at' => 'datetime',
            'restricted_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isTrusted(): bool
    {
        return $this->status === 'trusted';
    }

    public function isRestricted(): bool
    {
        return $this->status === 'restricted';
    }
}
