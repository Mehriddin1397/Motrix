<?php

namespace App\Filament\Resources\MotorcycleCategoryResource\Pages;

use App\Filament\Resources\MotorcycleCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMotorcycleCategory extends EditRecord
{
    protected static string $resource = MotorcycleCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
