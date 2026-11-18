<?php

namespace App\Filament\Resources\ShopResource\Pages;

use App\Filament\Resources\ShopResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Modules\Market\Models\Listing;
use Modules\Parts\Models\Part;

class EditShop extends EditRecord
{
    protected static string $resource = ShopResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $shop = $this->record;

        // Auto-assign roles and link listings
        if ($shop->user_id && $shop->user) {
            $user = $shop->user;
            if (in_array($shop->type, ['moto', 'both']) && ! $user->hasRole('moto-seller')) {
                $user->assignRole('moto-seller');
            }
            if (in_array($shop->type, ['parts', 'both']) && ! $user->hasRole('parts-seller')) {
                $user->assignRole('parts-seller');
            }

            Listing::where('user_id', $shop->user_id)->whereNull('shop_id')->update(['shop_id' => $shop->id]);
            Part::where('seller_id', $shop->user_id)->whereNull('shop_id')->update(['shop_id' => $shop->id]);
        }
    }
}
