<?php

namespace App\Filament\Resources\MotorcycleReviewResource\Pages;

use App\Filament\Resources\MotorcycleReviewResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageMotorcycleReviews extends ManageRecords
{
    protected static string $resource = MotorcycleReviewResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
