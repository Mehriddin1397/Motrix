<?php

namespace Modules\Parts\Models;

use App\Concerns\HasAdPromotions;
use App\Concerns\HasApprovalWorkflow;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Modules\Motorcycle\Models\Motorcycle;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Part extends Model implements HasMedia
{
    use HasAdPromotions, HasApprovalWorkflow, HasFactory, HasSlug, InteractsWithMedia;

    protected $fillable = [
        'seller_id',
        'category_id',
        'name',
        'slug',
        'part_type',
        'part_number',
        'price',
        'stock_qty',
        'condition',
        'description',
        'status',
        'promotion_tier',
        'promoted_until',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'promoted_until' => 'datetime',
        ];
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images');
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(PartCategory::class, 'category_id');
    }

    public function motorcycles(): BelongsToMany
    {
        return $this->belongsToMany(Motorcycle::class, 'part_motorcycle');
    }
}
