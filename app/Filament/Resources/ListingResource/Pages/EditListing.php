<?php

namespace App\Filament\Resources\ListingResource\Pages;

use App\Filament\Resources\ListingResource;
use App\Services\Social\ListingSocialPublisherService;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditListing extends EditRecord
{
    protected static string $resource = ListingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        /** @var \Modules\Market\Models\Listing $record */
        $record = $this->record;
        if ($record->status === 'sold') {
            app(ListingSocialPublisherService::class)->markAsSoldOnSocialMedia($record);
        }
    }
}
