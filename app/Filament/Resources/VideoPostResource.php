<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VideoPostResource\Pages;
use App\Jobs\PublishVideoToSocialMediaJob;
use App\Models\VideoPost;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class VideoPostResource extends Resource
{
    protected static ?string $model = VideoPost::class;

    protected static ?string $navigationIcon = 'heroicon-o-share';

    protected static ?string $navigationGroup = 'Ijtimoiy tarmoqlar';

    protected static ?string $navigationLabel = 'Video tarqatish';

    protected static ?string $modelLabel = 'video post';

    protected static ?string $pluralModelLabel = 'video postlar';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Video ma\'lumotlari')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Sarlavha')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (string $operation, $state, Forms\Set $set) => $operation === 'create' ? $set('slug', str($state)->slug()) : null),

                        Forms\Components\TextInput::make('slug')
                            ->label('Havola (Slug)')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),

                        Forms\Components\Textarea::make('description')
                            ->label('Tavsif / Matn (Tarmoqlardagi post matni va xeshteglar)')
                            ->rows(4)
                            ->columnSpanFull(),

                        Forms\Components\FileUpload::make('video_path')
                            ->label('Video fayli (.mp4, .mov)')
                            ->disk('public')
                            ->directory('videos')
                            ->acceptedFileTypes(['video/mp4', 'video/quicktime', 'video/x-msvideo'])
                            ->maxSize(512000) // 500 MB
                            ->required()
                            ->columnSpanFull(),

                        Forms\Components\FileUpload::make('thumbnail_path')
                            ->label('Muqova rasmi (Thumbnail, ixtiyoriy)')
                            ->image()
                            ->disk('public')
                            ->directory('videos/thumbnails'),
                    ])->columns(2),

                Forms\Components\Section::make('Ijtimoiy tarmoqlarga tarqatish')
                    ->description('Video yuklangach, orqa fonda quyidagi tanlangan platformalarga avtomatik joylanadi:')
                    ->schema([
                        Forms\Components\Toggle::make('send_to_telegram')
                            ->label('Telegram kanalga yuborish')
                            ->default(true),

                        Forms\Components\Toggle::make('send_to_youtube')
                            ->label('YouTube kanalga yuklash')
                            ->default(true),

                        Forms\Components\Toggle::make('send_to_facebook')
                            ->label('Facebook sahifaga joylash')
                            ->default(true),

                        Forms\Components\Toggle::make('send_to_instagram')
                            ->label('Instagram (Reels) ga yuklash')
                            ->default(true),
                    ])->columns(4),

                Forms\Components\Section::make('Tarqatish holatlari va xatoliklar jurnali')
                    ->collapsed()
                    ->visible(fn (?VideoPost $record) => $record !== null)
                    ->schema([
                        Forms\Components\Placeholder::make('telegram_info')
                            ->label('Telegram holati')
                            ->content(fn (?VideoPost $record) => $record ? "Holat: {$record->telegram_status}" . ($record->telegram_message_id ? " (Xabar ID: {$record->telegram_message_id})" : "") : '-'),

                        Forms\Components\Placeholder::make('youtube_info')
                            ->label('YouTube holati')
                            ->content(fn (?VideoPost $record) => $record ? "Holat: {$record->youtube_status}" . ($record->youtube_video_id ? " (Video ID: {$record->youtube_video_id})" : "") : '-'),

                        Forms\Components\Placeholder::make('facebook_info')
                            ->label('Facebook holati')
                            ->content(fn (?VideoPost $record) => $record ? "Holat: {$record->facebook_status}" . ($record->facebook_post_id ? " (Post ID: {$record->facebook_post_id})" : "") : '-'),

                        Forms\Components\Placeholder::make('instagram_info')
                            ->label('Instagram holati')
                            ->content(fn (?VideoPost $record) => $record ? "Holat: {$record->instagram_status}" . ($record->instagram_media_id ? " (Media ID: {$record->instagram_media_id})" : "") : '-'),

                        Forms\Components\Textarea::make('error_log')
                            ->label('Xatoliklar matni (Error Log)')
                            ->disabled()
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\ImageColumn::make('thumbnail_path')
                    ->label('Muqova')
                    ->disk('public'),

                Tables\Columns\TextColumn::make('title')
                    ->label('Sarlavha')
                    ->searchable()
                    ->limit(35),

                Tables\Columns\TextColumn::make('telegram_status')
                    ->label('Telegram')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'success' => 'success',
                        'failed' => 'danger',
                        'processing' => 'warning',
                        'skipped' => 'gray',
                        default => 'info',
                    }),

                Tables\Columns\TextColumn::make('youtube_status')
                    ->label('YouTube')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'success' => 'success',
                        'failed' => 'danger',
                        'processing' => 'warning',
                        'skipped' => 'gray',
                        default => 'info',
                    }),

                Tables\Columns\TextColumn::make('facebook_status')
                    ->label('Facebook')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'success' => 'success',
                        'failed' => 'danger',
                        'processing' => 'warning',
                        'skipped' => 'gray',
                        default => 'info',
                    }),

                Tables\Columns\TextColumn::make('instagram_status')
                    ->label('Instagram')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'success' => 'success',
                        'failed' => 'danger',
                        'processing' => 'warning',
                        'skipped' => 'gray',
                        default => 'info',
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Sana')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('retryPublish')
                    ->label('Qayta yuborish')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Tarmoqlarga qayta yuborish')
                    ->modalDescription('Ushbu videoni barcha kutilayotgan yoki xatolik bergan ijtimoiy tarmoqlarga qayta yuborishni xohlaysizmi?')
                    ->action(function (VideoPost $record) {
                        PublishVideoToSocialMediaJob::dispatch($record);
                        Notification::make()
                            ->title('Navbatga qo\'yildi!')
                            ->body('Video orqa fonda tarmoqlarga qayta yuklanmoqda.')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\Action::make('view_on_site')
                    ->label('Saytda')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn (VideoPost $record) => $record->public_url)
                    ->openUrlInNewTab(),

                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVideoPosts::route('/'),
            'create' => Pages\CreateVideoPost::route('/create'),
            'edit' => Pages\EditVideoPost::route('/{record}/edit'),
        ];
    }
}
