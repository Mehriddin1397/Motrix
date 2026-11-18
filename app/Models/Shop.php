<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Market\Models\Listing;
use Modules\Parts\Models\Part;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Shop extends Model implements HasMedia
{
    use HasFactory, HasSlug, InteractsWithMedia;

    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'type',
        'city_id',
        'address',
        'lat',
        'lng',
        'phone',
        'telegram',
        'instagram',
        'working_hours',
        'description',
        'logo',
        'is_verified',
        'is_active',
        'views_count',
    ];

    protected function casts(): array
    {
        return [
            'lat' => 'float',
            'lng' => 'float',
            'is_verified' => 'boolean',
            'is_active' => 'boolean',
            'views_count' => 'integer',
        ];
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug')
            ->doNotGenerateSlugsOnUpdate();
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('logo')->singleFile();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function listings(): HasMany
    {
        return $this->hasMany(Listing::class);
    }

    public function parts(): HasMany
    {
        return $this->hasMany(Part::class);
    }

    public function isMotoShop(): bool
    {
        return in_array($this->type, ['moto', 'both']);
    }

    public function isPartsShop(): bool
    {
        return in_array($this->type, ['parts', 'both']);
    }

    public function typeLabel(): string
    {
        return match ($this->type) {
            'moto' => 'Moto do\'kon',
            'parts' => 'Ehtiyot qism do\'koni',
            'both' => 'Moto va ehtiyot qism do\'koni',
            default => 'Do\'kon',
        };
    }

    public function hasCoordinates(): bool
    {
        return filled($this->lat) && filled($this->lng) && $this->lat != 0 && $this->lng != 0;
    }

    public function getYandexNaviUrl(): string
    {
        if ($this->hasCoordinates()) {
            return "https://yandex.uz/maps/?rtext=~{$this->lat},{$this->lng}&rtt=auto";
        }
        $query = urlencode(($this->address ?: '') . ' ' . ($this->city?->name ?: 'Uzbekistan'));
        return "https://yandex.uz/maps/?text={$query}";
    }

    public function getGoogleMapsUrl(): string
    {
        if ($this->hasCoordinates()) {
            return "https://www.google.com/maps/dir/?api=1&destination={$this->lat},{$this->lng}";
        }
        $query = urlencode(($this->address ?: '') . ' ' . ($this->city?->name ?: 'Uzbekistan'));
        return "https://www.google.com/maps/search/?api=1&query={$query}";
    }

    public function getAppleMapsUrl(): string
    {
        if ($this->hasCoordinates()) {
            return "https://maps.apple.com/?daddr={$this->lat},{$this->lng}&dirflg=d";
        }
        $query = urlencode(($this->address ?: '') . ' ' . ($this->city?->name ?: 'Uzbekistan'));
        return "https://maps.apple.com/?q={$query}";
    }

    public function getTwoGisUrl(): string
    {
        if ($this->hasCoordinates()) {
            return "https://2gis.uz/routeSearch/rsType/car/to/{$this->lng},{$this->lat}";
        }
        return "https://2gis.uz";
    }

    public function getLogoUrlAttribute(): ?string
    {
        if ($this->hasMedia('logo')) {
            return $this->getFirstMediaUrl('logo');
        }
        if (! empty($this->logo)) {
            return str_starts_with($this->logo, 'http') ? $this->logo : asset('storage/' . ltrim($this->logo, '/'));
        }
        return null;
    }
}
