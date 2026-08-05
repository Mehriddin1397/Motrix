<?php

namespace App\Filament\Resources\ListingReportResource\Pages;

use App\Filament\Resources\ListingReportResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageListingReports extends ManageRecords
{
    protected static string $resource = ListingReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
