<?php

namespace App\Filament\Resources\VideoPostResource\Pages;

use App\Filament\Resources\VideoPostResource;
use App\Jobs\PublishVideoToSocialMediaJob;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateVideoPost extends CreateRecord
{
    protected static string $resource = VideoPostResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        $data['send_to_facebook'] = false;

        return $data;
    }

    protected function afterCreate(): void
    {
        PublishVideoToSocialMediaJob::dispatch($this->record);

        Notification::make()
            ->title('Video saqlandi!')
            ->body('Video muvaffaqiyatli yuklandi va orqa fonda tanlangan ijtimoiy tarmoqlarga yuborilmoqda.')
            ->success()
            ->send();
    }
}
