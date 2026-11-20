<?php

namespace App\Filament\Resources\VideoPostResource\Pages;

use App\Filament\Resources\VideoPostResource;
use App\Jobs\PublishVideoToSocialMediaJob;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditVideoPost extends EditRecord
{
    protected static string $resource = VideoPostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('retryPublish')
                ->label('Tarmoqlarga qayta yuborish')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->requiresConfirmation()
                ->action(function () {
                    PublishVideoToSocialMediaJob::dispatch($this->record);
                    Notification::make()
                        ->title('Navbatga qo\'yildi!')
                        ->body('Video orqa fonda tarmoqlarga qayta yuklanmoqda.')
                        ->success()
                        ->send();
                }),
            Actions\Action::make('view_on_site')
                ->label('Saytda ko\'rish')
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->url(fn () => $this->record->public_url)
                ->openUrlInNewTab(),
            Actions\DeleteAction::make(),
        ];
    }
}
