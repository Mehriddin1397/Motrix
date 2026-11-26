<?php

namespace App\Filament\Resources\VideoPostResource\Pages;

use App\Filament\Resources\VideoPostResource;
use App\Jobs\PublishVideoToSocialMediaJob;
use Carbon\Carbon;
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

        // Agar scheduled_at kelajakda bo'lsa, holatlarni 'scheduled' qilib belgilaymiz
        if (! empty($data['scheduled_at']) && Carbon::parse($data['scheduled_at'])->isFuture()) {
            if (! empty($data['send_to_telegram'])) {
                $data['telegram_status'] = 'scheduled';
            }
            if (! empty($data['send_to_youtube'])) {
                $data['youtube_status'] = 'scheduled';
            }
            if (! empty($data['send_to_instagram'])) {
                $data['instagram_status'] = 'scheduled';
            }
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        if ($this->record->isScheduled()) {
            // Rejalashtirilgan vaqt kelganda bajarilishi uchun delay bilan navbatga qo'yiladi
            PublishVideoToSocialMediaJob::dispatch($this->record)->delay($this->record->scheduled_at);

            Notification::make()
                ->title('Video muvaffaqiyatli rejalashtirildi! ⏰')
                ->body("Video belgilangan vaqtda ({$this->record->scheduled_at->format('d.m.Y H:i')}) avtomatik tarzda ijtimoiy tarmoqlarga yuboriladi.")
                ->info()
                ->send();
        } else {
            PublishVideoToSocialMediaJob::dispatch($this->record);

            Notification::make()
                ->title('Video saqlandi!')
                ->body('Video muvaffaqiyatli yuklandi va orqa fonda tanlangan ijtimoiy tarmoqlarga yuborilmoqda.')
                ->success()
                ->send();
        }
    }
}
