<?php

namespace Modules\Market\Models;

use App\Concerns\HasAdPromotions;
use App\Concerns\HasApprovalWorkflow;
use App\Models\City;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Brand\Models\Brand;
use Modules\Motorcycle\Models\Motorcycle;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Listing extends Model implements HasMedia
{
    use HasAdPromotions, HasApprovalWorkflow, HasFactory, InteractsWithMedia;

    protected $fillable = [
        'user_id',
        'motorcycle_id',
        'brand_id',
        'custom_title',
        'year',
        'price',
        'currency',
        'mileage_km',
        'condition',
        'city_id',
        'description',
        'status',
        'is_featured',
        'promotion_tier',
        'promoted_until',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_featured' => 'boolean',
            'promoted_until' => 'datetime',
            'published_at' => 'datetime',
        ];
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images');
        $this->addMediaCollection('videos');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function motorcycle(): BelongsTo
    {
        return $this->belongsTo(Motorcycle::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function reports(): HasMany
    {
        return $this->hasMany(ListingReport::class);
    }

    public function conversations(): HasMany
    {
        return $this->hasMany(Conversation::class);
    }
}
