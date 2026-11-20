<?php

namespace App\Filament\Resources\VideoPostResource\Pages;

use App\Filament\Resources\VideoPostResource;
use App\Models\Setting;
use App\Services\Social\InstagramPublisherService;
use App\Services\Social\YouTubePublisherService;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListVideoPosts extends ListRecords
{
    protected static string $resource = VideoPostResource::class;

    protected function getHeaderActions(): array
    {
        $youtubeService = app(YouTubePublisherService::class);
        $instagramService = app(InstagramPublisherService::class);

        return [
            Actions\CreateAction::make()->label('Yangi video yuklash'),

            Actions\Action::make('connect_youtube')
                ->label($youtubeService->isConfigured() ? 'YouTube: Ulangan ✅' : 'YouTube kanalni ulash')
                ->icon('heroicon-o-play')
                ->color($youtubeService->isConfigured() ? 'success' : 'danger')
                ->tooltip('Google hisobingiz orqali YouTube kanalni avtomatik ulash')
                ->url(route('admin.youtube.auth'))
                ->openUrlInNewTab(),

            Actions\Action::make('instagram_settings')
                ->label($instagramService->isConfigured() ? 'Instagram: Ulangan ✅' : 'Instagram sozlamalari')
                ->icon('heroicon-o-camera')
                ->color($instagramService->isConfigured() ? 'success' : 'gray')
                ->modalHeading('Instagram & Meta Sozlamalari')
                ->modalDescription('Instagram Reels va Facebook sahifaga video joylash uchun Meta Graph API ma\'lumotlari:')
                ->form([
                    Forms\Components\TextInput::make('meta_page_access_token')
                        ->label('Meta Page Access Token')
                        ->placeholder('EAAG...')
                        ->default(fn () => Setting::get('meta_page_access_token', config('services.meta.page_access_token')))
                        ->password()
                        ->revealable()
                        ->required()
                        ->helperText('developers.facebook.com orqali olingan doimiy Page Access Token'),

                    Forms\Components\TextInput::make('instagram_account_id')
                        ->label('Instagram Professional Account ID')
                        ->placeholder('178414...')
                        ->default(fn () => Setting::get('instagram_account_id', config('services.meta.instagram_account_id')))
                        ->required()
                        ->helperText('Facebook sahifangizga ulangan Instagram hisob ID raqami'),

                    Forms\Components\TextInput::make('telegram_group_id')
                        ->label('Telegram Guruh ID yoki username (ixtiyoriy)')
                        ->placeholder('Masalan: @motrix_group yoki -100...')
                        ->default(fn () => Setting::get('telegram_group_id', config('services.telegram.group_id')))
                        ->helperText('Agar guruhga ham video tashlanishini xohlasangiz, guruh username yoki ID sini kiriting (bot guruhda ham admin/a\'zo bo\'lishi kerak).'),
                ])
                ->action(function (array $data) {
                    Setting::set('meta_page_access_token', $data['meta_page_access_token']);
                    Setting::set('instagram_account_id', $data['instagram_account_id']);
                    if (! empty($data['telegram_group_id'])) {
                        Setting::set('telegram_group_id', $data['telegram_group_id']);
                    }

                    Notification::make()
                        ->title('Sozlamalar saqlandi!')
                        ->body('Instagram va Telegram guruh sozlamalari yangilandi.')
                        ->success()
                        ->send();
                }),
        ];
    }
}
