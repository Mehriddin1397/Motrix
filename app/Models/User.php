<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser, HasMedia
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, HasRoles, InteractsWithMedia, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'phone',
        'password',
        'bio',
        'avatar',
        'telegram',
        'instagram',
        'city_id',
        'experience_level',
        'height_cm',
        'budget_usd',
        'google_id',
        'email_verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'last_active_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->can('platform.access-admin');
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function trustProfile(): HasOne
    {
        return $this->hasOne(SellerTrustProfile::class);
    }

    public function adPromotions(): HasMany
    {
        return $this->hasMany(AdPromotion::class);
    }

    public function isMotoSeller(): bool
    {
        return $this->hasRole('moto-seller');
    }

    public function isPartsSeller(): bool
    {
        return $this->hasRole('parts-seller');
    }

    public function shops(): HasMany
    {
        return $this->hasMany(Shop::class);
    }

    public function shop(): HasOne
    {
        return $this->hasOne(Shop::class)->latestOfMany();
    }

    public function motoShop(): HasOne
    {
        return $this->hasOne(Shop::class)
            ->whereIn('type', ['moto', 'both'])
            ->where('is_active', true)
            ->latestOfMany();
    }

    public function partsShop(): HasOne
    {
        return $this->hasOne(Shop::class)
            ->whereIn('type', ['parts', 'both'])
            ->where('is_active', true)
            ->latestOfMany();
    }

    public function shopRequests(): HasMany
    {
        return $this->hasMany(ShopRequest::class);
    }

    public function isMotoShopSeller(): bool
    {
        return $this->motoShop()->exists();
    }

    public function isPartsShopSeller(): bool
    {
        return $this->partsShop()->exists();
    }

    public function sellerTypeLabel(): string
    {
        if ($this->isMotoShopSeller()) {
            return 'Moto sotuvchi do\'kon';
        }

        return 'Oddiy sotuvchi';
    }

    public function savedItems(): HasMany
    {
        return $this->hasMany(SavedItem::class);
    }

    public function following(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_follows', 'follower_id', 'following_id')
            ->withTimestamps();
    }

    public function followers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_follows', 'following_id', 'follower_id')
            ->withTimestamps();
    }

    public function listings(): HasMany
    {
        return $this->hasMany(\Modules\Market\Models\Listing::class);
    }

    public function getTelegramUrlAttribute(): ?string
    {
        if (blank($this->telegram)) {
            return null;
        }

        $clean = trim($this->telegram);
        if (str_starts_with($clean, 'http://') || str_starts_with($clean, 'https://')) {
            return $clean;
        }

        $username = ltrim($clean, '@');
        return "https://t.me/{$username}";
    }

    public function getTelegramHandleAttribute(): ?string
    {
        if (blank($this->telegram)) {
            return null;
        }

        $clean = trim($this->telegram);
        if (str_contains($clean, 't.me/')) {
            $parts = explode('t.me/', $clean);
            return '@' . trim(end($parts), '/');
        }

        return str_starts_with($clean, '@') ? $clean : "@{$clean}";
    }

    public function getInstagramUrlAttribute(): ?string
    {
        if (blank($this->instagram)) {
            return null;
        }

        $clean = trim($this->instagram);
        if (str_starts_with($clean, 'http://') || str_starts_with($clean, 'https://')) {
            return $clean;
        }

        $username = ltrim($clean, '@');
        return "https://instagram.com/{$username}";
    }

    public function getInstagramHandleAttribute(): ?string
    {
        if (blank($this->instagram)) {
            return null;
        }

        $clean = trim($this->instagram);
        if (str_contains($clean, 'instagram.com/')) {
            $parts = explode('instagram.com/', $clean);
            return '@' . trim(end($parts), '/');
        }

        return str_starts_with($clean, '@') ? $clean : "@{$clean}";
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('avatar')->singleFile();
    }

    public function getAvatarUrlAttribute(): ?string
    {
        $url = null;
        if ($this->hasMedia('avatar')) {
            $url = $this->getFirstMediaUrl('avatar');
        } elseif (! empty($this->avatar)) {
            $url = $this->avatar;
        }

        if (! $url) {
            return null;
        }

        // Agar nisbiy yo'l bo'lsa (masalan: "avatars/1.jpg" yoki "storage/...")
        if (! str_starts_with($url, 'http://') && ! str_starts_with($url, 'https://')) {
            $trimmed = ltrim($url, '/');
            if (str_starts_with($trimmed, 'storage/')) {
                return asset($trimmed);
            }
            return asset('storage/' . $trimmed);
        }

        $host = parse_url($url, PHP_URL_HOST);
        $path = parse_url($url, PHP_URL_PATH) ?: '';

        // Agar URL ichida /storage/ bo'lsa (masalan http://localhost/storage/...)
        if (str_contains($path, '/storage/')) {
            $storagePath = substr($path, strpos($path, '/storage/') + 1);
            return asset($storagePath);
        }

        // Agar Google yoki boshqa tashqi domen bo'lsa, URL'ni o'zini qaytaramiz
        $appHost = parse_url(config('app.url'), PHP_URL_HOST);
        $requestHost = request()?->getHost();
        $localHosts = array_filter(['localhost', '127.0.0.1', $appHost, $requestHost]);

        if (! in_array($host, $localHosts, true)) {
            // Google avatar o'lchamini kattaroq (s256-c) qilish
            if (str_contains($host, 'googleusercontent.com')) {
                $url = preg_replace('/=s\d+(-c)?$/', '=s256-c', $url);
            }
            return $url;
        }

        // Mahalliy serverdagi boshqa yo'llar
        return asset(ltrim($path, '/'));
    }
}
