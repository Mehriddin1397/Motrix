<?php

namespace App\Filament\Resources\VideoPostResource\Pages;

use App\Filament\Resources\VideoPostResource;
use App\Jobs\PublishVideoToSocialMediaJob;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditVideoPost extends EditRecord
{
    protected static string $resource = VideoPostResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Agar scheduled_at kelajakda bo'lsa va hali yuborilmagan bo'lsa
        if (! empty($data['scheduled_at']) && Carbon::parse($data['scheduled_at'])->isFuture()) {
            if ($this->record->telegram_status !== 'success' && ! empty($data['send_to_telegram'])) {
                $data['telegram_status'] = 'scheduled';
            }
            if ($this->record->youtube_status !== 'success' && ! empty($data['send_to_youtube'])) {
                $data['youtube_status'] = 'scheduled';
            }
            if ($this->record->instagram_status !== 'success' && ! empty($data['send_to_instagram'])) {
                $data['instagram_status'] = 'scheduled';
            }
        }

        return $data;
    }

    protected function afterSave(): void
    {
        if ($this->record->isScheduled()) {
            PublishVideoToSocialMediaJob::dispatch($this->record)->delay($this->record->scheduled_at);

            Notification::make()
                ->title('Rejalashtirilgan vaqt yangilandi! ⏰')
                ->body("Video belgilangan vaqtda ({$this->record->scheduled_at->format('d.m.Y H:i')}) yuboriladi.")
                ->info()
                ->send();
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('publishNow')
                ->label('Hozir darhol yuborish')
                ->icon('heroicon-m-paper-airplane')
                ->color('success')
                ->visible(fn () => $this->record->isScheduled() || $this->record->telegram_status === 'scheduled')
                ->requiresConfirmation()
                ->modalHeading('Videoni hozir darhol yuborish')
                ->modalDescription('Rejalashtirilgan vaqtni kutmasdan, videoni hoziroq barcha tanlangan ijtimoiy tarmoqlarga yuborishni xohlaysizmi?')
                ->action(function () {
                    $this->record->update([
                        'scheduled_at' => now(),
                        'telegram_status' => $this->record->send_to_telegram && $this->record->telegram_status !== 'success' ? 'pending' : $this->record->telegram_status,
                        'youtube_status' => $this->record->send_to_youtube && $this->record->youtube_status !== 'success' ? 'pending' : $this->record->youtube_status,
                        'instagram_status' => $this->record->send_to_instagram && $this->record->instagram_status !== 'success' ? 'pending' : $this->record->instagram_status,
                    ]);

                    PublishVideoToSocialMediaJob::dispatch($this->record, true);

                    Notification::make()
                        ->title('Yuborish boshlandi!')
                        ->body('Video tarmoqlarga darhol yuklanmoqda.')
                        ->success()
                        ->send();
                }),

            Actions\Action::make('retryPublish')
                ->label('Tarmoqlarga qayta yuborish')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->requiresConfirmation()
                ->action(function () {
                    PublishVideoToSocialMediaJob::dispatch($this->record, true);
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
