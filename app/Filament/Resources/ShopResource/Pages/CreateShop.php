<?php

namespace App\Filament\Resources\ShopResource\Pages;

use App\Filament\Resources\ShopResource;
use Filament\Resources\Pages\CreateRecord;
use Modules\Market\Models\Listing;
use Modules\Parts\Models\Part;

class CreateShop extends CreateRecord
{
    protected static string $resource = ShopResource::class;

    protected function afterCreate(): void
    {
        $shop = $this->record;

        // Auto-assign roles and link existing listings if a user is assigned
        if ($shop->user_id && $shop->user) {
            $user = $shop->user;
            if (in_array($shop->type, ['moto', 'both']) && ! $user->hasRole('moto-seller')) {
                $user->assignRole('moto-seller');
            }
            if (in_array($shop->type, ['parts', 'both']) && ! $user->hasRole('parts-seller')) {
                $user->assignRole('parts-seller');
            }

            // Link existing user listings or parts to this shop
            Listing::where('user_id', $shop->user_id)->whereNull('shop_id')->update(['shop_id' => $shop->id]);
            Part::where('seller_id', $shop->user_id)->whereNull('shop_id')->update(['shop_id' => $shop->id]);
        }
    }
}
