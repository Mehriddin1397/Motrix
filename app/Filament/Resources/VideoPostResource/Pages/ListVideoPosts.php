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

                    Forms\Components\TextInput::make('facebook_page_id')
                        ->label('Facebook Page ID (ixtiyoriy)')
                        ->placeholder('1092837465...')
                        ->default(fn () => Setting::get('facebook_page_id', config('services.meta.facebook_page_id'))),
                ])
                ->action(function (array $data) {
                    Setting::set('meta_page_access_token', $data['meta_page_access_token']);
                    Setting::set('instagram_account_id', $data['instagram_account_id']);
                    if (! empty($data['facebook_page_id'])) {
                        Setting::set('facebook_page_id', $data['facebook_page_id']);
                    }

                    Notification::make()
                        ->title('Meta sozlamalari saqlandi!')
                        ->body('Instagram va Facebook ulanish ma\'lumotlari yangilandi.')
                        ->success()
                        ->send();
                }),
        ];
    }
}
