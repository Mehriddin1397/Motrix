<?php

namespace Modules\Motorcycle\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Modules\Brand\Models\Brand;
use Modules\Parts\Models\Part;
use Modules\Review\Models\MotorcycleReview;
use Modules\Video\Models\Video;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Motorcycle extends Model implements HasMedia
{
    use HasFactory, HasSlug, InteractsWithMedia;

    protected $fillable = [
        'brand_id',
        'category_id',
        'name',
        'slug',
        'generation',
        'year_start',
        'year_end',
        'history',
        'description',
        'status',
        'meta_title',
        'meta_description',
    ];

    protected function casts(): array
    {
        return [
            'views_count' => 'integer',
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
        $this->addMediaCollection('cover')->singleFile();
        $this->addMediaCollection('gallery');
        $this->addMediaCollection('engine');
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(MotorcycleCategory::class, 'category_id');
    }

    public function specification(): HasOne
    {
        return $this->hasOne(MotorcycleSpecification::class);
    }

    public function prosCons(): HasMany
    {
        return $this->hasMany(MotorcycleProCon::class);
    }

    public function engineDetail(): HasOne
    {
        return $this->hasOne(MotorcycleEngineDetail::class);
    }

    public function relatedMotorcycles(): BelongsToMany
    {
        return $this->belongsToMany(
            Motorcycle::class,
            'motorcycle_related',
            'motorcycle_id',
            'related_motorcycle_id'
        )->withPivot('is_manual');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(MotorcycleReview::class);
    }

    public function videos(): HasMany
    {
        return $this->hasMany(Video::class);
    }

    public function parts(): BelongsToMany
    {
        return $this->belongsToMany(Part::class, 'part_motorcycle');
    }
}
