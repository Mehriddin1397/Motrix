<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Market\Models\Listing;
use Modules\Parts\Models\Part;

class ShopRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'shop_name',
        'shop_type',
        'city_id',
        'address',
        'lat',
        'lng',
        'phone',
        'message',
        'status',
        'admin_notes',
        'processed_by',
        'processed_at',
    ];

    protected function casts(): array
    {
        return [
            'lat' => 'float',
            'lng' => 'float',
            'processed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function processor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function typeLabel(): string
    {
        return match ($this->shop_type) {
            'moto' => 'Moto do\'kon',
            'parts' => 'Ehtiyot qism do\'koni',
            'both' => 'Moto va ehtiyot qism do\'koni',
            default => 'Do\'kon',
        };
    }

    /**
     * Approve this application and instantiate a Shop assigned to the applicant.
     */
    public function approve(User $admin, ?array $overrides = []): Shop
    {
        $shopData = array_merge([
            'user_id' => $this->user_id,
            'name' => $this->shop_name,
            'type' => $this->shop_type,
            'city_id' => $this->city_id,
            'address' => $this->address,
            'lat' => $this->lat,
            'lng' => $this->lng,
            'phone' => $this->phone,
            'description' => $this->message,
            'is_verified' => true,
            'is_active' => true,
        ], $overrides);

        $shop = Shop::create($shopData);

        // Assign seller role to applicant
        if ($this->user) {
            if (in_array($this->shop_type, ['moto', 'both']) && ! $this->user->hasRole('moto-seller')) {
                $this->user->assignRole('moto-seller');
            }
            if (in_array($this->shop_type, ['parts', 'both']) && ! $this->user->hasRole('parts-seller')) {
                $this->user->assignRole('parts-seller');
            }

            // Link existing listings or parts of this user to the newly created shop
            Listing::where('user_id', $this->user_id)->whereNull('shop_id')->update(['shop_id' => $shop->id]);
            Part::where('seller_id', $this->user_id)->whereNull('shop_id')->update(['shop_id' => $shop->id]);
        }

        $this->update([
            'status' => 'approved',
            'processed_by' => $admin->id,
            'processed_at' => now(),
        ]);

        // Foydalanuvchiga arizasi qabul qilingani haqida SMS xabar yuborish
        app(\App\Services\EskizSmsService::class)->notifyUserRequestApproved($this);

        return $shop;
    }

    /**
     * Reject this application.
     */
    public function reject(User $admin, ?string $reason = null): void
    {
        $this->update([
            'status' => 'rejected',
            'admin_notes' => $reason,
            'processed_by' => $admin->id,
            'processed_at' => now(),
        ]);
    }
}
