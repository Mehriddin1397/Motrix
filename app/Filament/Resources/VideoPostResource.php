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
                            ->label('Video yuzi / Muqova rasmi (Thumbnail)')
                            ->image()
                            ->imageEditor()
                            ->disk('public')
                            ->directory('videos/thumbnails')
                            ->helperText('Ushbu rasm Telegram, YouTube, Instagram Reels va Facebook tarmoqlarida video muqovasi (birinchi ko\'rinadigan yuz rasmi) sifatida o\'rnatiladi.')
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Nashr vaqti va Rejalashtirish')
                    ->description('Videoni hozir darhol yuklash yoki kelajakdagi aniq sana va vaqtga rejalashtirish (O\'zbekiston vaqti bilan, UTC+5):')
                    ->schema([
                        Forms\Components\DateTimePicker::make('scheduled_at')
                            ->label('Rejalashtirilgan nashr vaqti (Toshkent vaqti, UTC+5)')
                            ->placeholder('Hozir darhol yuklash uchun bo\'sh qoldiring')
                            ->helperText('Agar kelajakdagi sana va vaqtni tanlasangiz, tizim o\'sha vaqt yetganda videoni avtomatik tarzda barcha tanlangan tarmoqlarga joylaydi.')
                            ->seconds(false)
                            ->timezone('Asia/Tashkent')
                            ->native(false),

                        Forms\Components\DateTimePicker::make('published_at')
                            ->label('Dastlabki yaratilgan sana')
                            ->timezone('Asia/Tashkent')
                            ->disabled()
                            ->visible(fn (?VideoPost $record) => $record !== null),
                    ])->columns(2),

                Forms\Components\Section::make('Ijtimoiy tarmoqlarga tarqatish')
                    ->description('Video yuklangach yoki rejalashtirilgan vaqt yetganda, quyidagi tanlangan platformalarga avtomatik joylanadi:')
                    ->schema([
                        Forms\Components\Toggle::make('send_to_telegram')
                            ->label('Telegram (Kanal va Guruh)')
                            ->default(true),

                        Forms\Components\Toggle::make('send_to_youtube')
                            ->label('YouTube kanalga yuklash')
                            ->default(true),

                        Forms\Components\Toggle::make('send_to_instagram')
                            ->label('Instagram (Reels) ga yuklash')
                            ->default(true),
                    ])->columns(3),

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

                        Forms\Components\Placeholder::make('instagram_info')
                            ->label('Instagram holati')
                            ->content(fn (?VideoPost $record) => $record ? "Holat: {$record->instagram_status}" . ($record->instagram_media_id ? " (Media ID: {$record->instagram_media_id})" : "") : '-'),

                        Forms\Components\Textarea::make('error_log')
                            ->label('Xatoliklar matni (Error Log)')
                            ->disabled()
                            ->columnSpanFull(),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\ImageColumn::make('thumbnail_path')
                    ->label('Muqova')
                    ->disk('public')
                    ->square(),

                Tables\Columns\TextColumn::make('title')
                    ->label('Sarlavha')
                    ->searchable()
                    ->limit(30),

                Tables\Columns\TextColumn::make('scheduled_at')
                    ->label('Rejalashtirilgan vaqt')
                    ->timezone('Asia/Tashkent')
                    ->dateTime('d.m.Y H:i')
                    ->placeholder('Darhol')
                    ->sortable()
                    ->badge()
                    ->color(fn (?VideoPost $record): string => ($record && $record->isScheduled()) ? 'warning' : 'gray'),

                Tables\Columns\TextColumn::make('telegram_status')
                    ->label('Telegram')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'success' => 'success',
                        'failed' => 'danger',
                        'processing' => 'warning',
                        'scheduled' => 'info',
                        'skipped' => 'gray',
                        default => 'info',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'scheduled' => '⏰ Rejalashtirilgan',
                        'processing' => '⏳ Yuklanmoqda',
                        'success' => '✅ Yuklandi',
                        'failed' => '❌ Xatolik',
                        'skipped' => 'O\'tkazib yuborilgan',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('youtube_status')
                    ->label('YouTube')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'success' => 'success',
                        'failed' => 'danger',
                        'processing' => 'warning',
                        'scheduled' => 'info',
                        'skipped' => 'gray',
                        default => 'info',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'scheduled' => '⏰ Rejalashtirilgan',
                        'processing' => '⏳ Yuklanmoqda',
                        'success' => '✅ Yuklandi',
                        'failed' => '❌ Xatolik',
                        'skipped' => 'O\'tkazib yuborilgan',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('instagram_status')
                    ->label('Instagram')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'success' => 'success',
                        'failed' => 'danger',
                        'processing' => 'warning',
                        'scheduled' => 'info',
                        'skipped' => 'gray',
                        default => 'info',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'scheduled' => '⏰ Rejalashtirilgan',
                        'processing' => '⏳ Yuklanmoqda',
                        'success' => '✅ Yuklandi',
                        'failed' => '❌ Xatolik',
                        'skipped' => 'O\'tkazib yuborilgan',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Yaratilgan sana')
                    ->timezone('Asia/Tashkent')
                    ->dateTime('d.m.Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\Filter::make('is_scheduled')
                    ->label('⏰ Faqat rejalashtirilganlar')
                    ->query(fn ($query) => $query->whereNotNull('scheduled_at')->where('scheduled_at', '>', now())),

                Tables\Filters\SelectFilter::make('telegram_status')
                    ->label('Telegram holati')
                    ->options([
                        'scheduled' => 'Rejalashtirilgan',
                        'pending' => 'Kutilmoqda',
                        'processing' => 'Yuklanmoqda',
                        'success' => 'Yuklandi',
                        'failed' => 'Xatolik',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('publishNow')
                    ->label('Hozir yuborish')
                    ->icon('heroicon-m-paper-airplane')
                    ->color('success')
                    ->visible(fn (VideoPost $record) => $record->isScheduled() || $record->telegram_status === 'scheduled')
                    ->requiresConfirmation()
                    ->modalHeading('Videoni hozir darhol yuborish')
                    ->modalDescription('Rejalashtirilgan vaqtni kutmasdan, videoni hoziroq barcha tanlangan tarmoqlarga yuborishni xohlaysizmi?')
                    ->action(function (VideoPost $record) {
                        $record->update([
                            'scheduled_at' => now(),
                            'telegram_status' => $record->send_to_telegram && $record->telegram_status !== 'success' ? 'pending' : $record->telegram_status,
                            'youtube_status' => $record->send_to_youtube && $record->youtube_status !== 'success' ? 'pending' : $record->youtube_status,
                            'instagram_status' => $record->send_to_instagram && $record->instagram_status !== 'success' ? 'pending' : $record->instagram_status,
                        ]);

                        PublishVideoToSocialMediaJob::dispatch($record, true);

                        Notification::make()
                            ->title('Yuborish boshlandi!')
                            ->body('Video tarmoqlarga darhol yuklanmoqda.')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\Action::make('retryPublish')
                    ->label('Qayta yuborish')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->visible(fn (VideoPost $record) => ! $record->isScheduled() && ($record->telegram_status === 'failed' || $record->youtube_status === 'failed' || $record->instagram_status === 'failed'))
                    ->requiresConfirmation()
                    ->modalHeading('Tarmoqlarga qayta yuborish')
                    ->modalDescription('Ushbu videoni barcha kutilayotgan yoki xatolik bergan ijtimoiy tarmoqlarga qayta yuborishni xohlaysizmi?')
                    ->action(function (VideoPost $record) {
                        PublishVideoToSocialMediaJob::dispatch($record, true);
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
