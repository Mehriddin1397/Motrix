<?php

namespace App\Filament\Resources\MotorcycleCategoryResource\Pages;

use App\Filament\Resources\MotorcycleCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMotorcycleCategories extends ListRecords
{
    protected static string $resource = MotorcycleCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
