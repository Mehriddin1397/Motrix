<?php

namespace Modules\ServiceCenter\Models;

use App\Models\City;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Brand\Models\Brand;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class ServiceProvider extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'user_id',
        'category_id',
        'name',
        'city_id',
        'address',
        'lat',
        'lng',
        'phone',
        'working_hours',
        'description',
        'verified',
        'rating_avg',
    ];

    protected function casts(): array
    {
        return [
            'working_hours' => 'array',
            'verified' => 'boolean',
            'rating_avg' => 'decimal:1',
        ];
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('logo')->singleFile();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ServiceCategory::class, 'category_id');
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function brands(): BelongsToMany
    {
        return $this->belongsToMany(Brand::class, 'service_provider_brand');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(ServiceReview::class);
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
}
