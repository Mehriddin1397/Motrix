<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ListingResource\Pages;
use App\Filament\Resources\ListingResource\RelationManagers;
use App\Models\City;
use App\Models\Country;
use App\Services\Social\ListingSocialPublisherService;
use Filament\Notifications\Notification;
use Modules\Market\Models\Listing;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ListingResource extends Resource
{
    protected static ?string $model = Listing::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Bozor';

    protected static ?string $navigationLabel = 'E\'lonlar';

    protected static ?string $modelLabel = 'e\'lon';

    protected static ?string $pluralModelLabel = 'e\'lonlar';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->label('Sotuvchi')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\Select::make('motorcycle_id')
                    ->label('Mototsikl')
                    ->relationship('motorcycle', 'name')
                    ->searchable()
                    ->preload(),
                Forms\Components\Select::make('brand_id')
                    ->label('Brend')
                    ->relationship('brand', 'name')
                    ->searchable()
                    ->preload(),
                Forms\Components\TextInput::make('custom_title')
                    ->label('Sarlavha (ixtiyoriy)'),
                Forms\Components\TextInput::make('year')
                    ->label('Yili')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('price')
                    ->label('Narx')
                    ->required()
                    ->numeric()
                    ->prefix('$'),
                Forms\Components\Select::make('currency')
                    ->label('Valyuta')
                    ->options(['USD' => 'USD', 'UZS' => 'UZS'])
                    ->default('USD')
                    ->required(),
                Forms\Components\TextInput::make('mileage_km')
                    ->label('Probeg (km)')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\Select::make('condition')
                    ->label('Texnik holati')
                    ->options(['new' => 'Yangi', 'used' => 'Ishlatilgan'])
                    ->required(),
                Forms\Components\Select::make('city_id')
                    ->label('Shahar')
                    ->relationship('city', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->createOptionForm([
                        Forms\Components\TextInput::make('name')
                            ->label('Nomi')
                            ->required(),
                    ])
                    ->createOptionUsing(function (array $data): int {
                        $data['country_id'] = Country::where('code', 'UZ')->value('id');

                        return City::create($data)->getKey();
                    })
                    ->createOptionModalHeading('Yangi shahar qo\'shish'),
                Forms\Components\Textarea::make('description')
                    ->label('Tavsif')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\Select::make('status')
                    ->label('E\'lon holati')
                    ->options([
                        'pending' => 'Kutilmoqda',
                        'active' => 'Faol',
                        'sold' => 'Sotilgan',
                        'rejected' => 'Rad etilgan',
                        'expired' => 'Muddati o\'tgan',
                    ])
                    ->default('pending')
                    ->required(),
                Forms\Components\Toggle::make('is_featured')
                    ->label('Tavsiya etilgan'),
                Forms\Components\DateTimePicker::make('published_at')
                    ->label('Chop etilgan sana'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Sotuvchi')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('motorcycle.name')
                    ->label('Mototsikl')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('brand.name')
                    ->label('Brend')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('custom_title')
                    ->label('Sarlavha')
                    ->searchable(),
                Tables\Columns\TextColumn::make('year')
                    ->label('Yili')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('price')
                    ->label('Narx')
                    ->money()
                    ->sortable(),
                Tables\Columns\TextColumn::make('currency')
                    ->label('Valyuta')
                    ->searchable(),
                Tables\Columns\TextColumn::make('mileage_km')
                    ->label('Probeg (km)')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('condition')
                    ->label('Texnik holati')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'new' => 'Yangi',
                        'used' => 'Ishlatilgan',
                        default => $state,
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('city.name')
                    ->label('Shahar')
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Holati')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Kutilmoqda',
                        'active' => 'Faol',
                        'sold' => 'Sotilgan',
                        'rejected' => 'Rad etilgan',
                        'expired' => 'Muddati o\'tgan',
                        default => $state,
                    })
                    ->colors([
                        'gray' => 'pending',
                        'success' => 'active',
                        'info' => 'sold',
                        'danger' => 'rejected',
                        'warning' => 'expired',
                    ]),
                Tables\Columns\BadgeColumn::make('telegram_status')
                    ->label('Telegram')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'published' => 'Yuborilgan',
                        'failed' => 'Xatolik',
                        default => 'Yuborilmagan',
                    })
                    ->colors([
                        'success' => 'published',
                        'danger' => 'failed',
                        'gray' => fn ($state) => $state !== 'published' && $state !== 'failed',
                    ])
                    ->toggleable(),
                Tables\Columns\BadgeColumn::make('instagram_status')
                    ->label('Instagram')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'published' => 'Yuborilgan',
                        'failed' => 'Xatolik',
                        default => 'Yuborilmagan',
                    })
                    ->colors([
                        'success' => 'published',
                        'danger' => 'failed',
                        'gray' => fn ($state) => $state !== 'published' && $state !== 'failed',
                    ])
                    ->toggleable(),
                Tables\Columns\IconColumn::make('is_featured')
                    ->label('Tavsiya etilgan')
                    ->boolean(),
                Tables\Columns\TextColumn::make('views_count')
                    ->label('Ko\'rishlar soni')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('published_at')
                    ->label('Chop etilgan sana')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Yaratilgan')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Yangilangan')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Holati')
                    ->options([
                        'pending' => 'Kutilmoqda',
                        'active' => 'Faol',
                        'sold' => 'Sotilgan',
                        'rejected' => 'Rad etilgan',
                        'expired' => 'Muddati o\'tgan',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('publish_social')
                    ->label('Tarmoqlarga')
                    ->icon('heroicon-o-share')
                    ->color('primary')
                    ->form([
                        Forms\Components\Checkbox::make('send_telegram')
                            ->label('Telegram (@Motrixuz kanal va @Motrix_uz guruh)')
                            ->default(true),
                        Forms\Components\Checkbox::make('send_instagram')
                            ->label('Instagram (@motrix.uz)')
                            ->default(true),
                    ])
                    ->action(function (Listing $record, array $data) {
                        $publisher = app(ListingSocialPublisherService::class);
                        $messages = [];

                        if (! empty($data['send_telegram'])) {
                            try {
                                $publisher->publishToTelegram($record);
                                $messages[] = 'Telegram kanal va guruhga muvaffaqiyatli yuborildi!';
                            } catch (\Throwable $e) {
                                $record->update(['telegram_status' => 'failed']);
                                Notification::make()
                                    ->title('Telegram xatosi')
                                    ->body($e->getMessage())
                                    ->danger()
                                    ->send();
                            }
                        }

                        if (! empty($data['send_instagram'])) {
                            try {
                                $publisher->publishToInstagram($record);
                                $messages[] = 'Instagram sahifasiga muvaffaqiyatli post qilindi!';
                            } catch (\Throwable $e) {
                                $record->update(['instagram_status' => 'failed']);
                                Notification::make()
                                    ->title('Instagram xatosi')
                                    ->body($e->getMessage())
                                    ->danger()
                                    ->send();
                            }
                        }

                        if (! empty($messages)) {
                            Notification::make()
                                ->title('E\'lon tarqatildi!')
                                ->body(implode("\n", $messages))
                                ->success()
                                ->send();
                        }
                    }),

                Tables\Actions\Action::make('mark_sold')
                    ->label('Sotildi')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->visible(fn (Listing $record) => $record->status !== 'sold')
                    ->requiresConfirmation()
                    ->modalHeading('E\'lonni sotildi deb belgilash')
                    ->modalDescription('Ushbu mototsikl sotilganini tasdiqlaysizmi? Telegramdagi post sarlavhasi avtomatik tarzda "❌ SOTILDI" ga o\'zgartiriladi va sotuvchi telefoni yashiriladi.')
                    ->action(function (Listing $record) {
                        $record->update(['status' => 'sold']);
                        $publisher = app(ListingSocialPublisherService::class);
                        $publisher->markAsSoldOnSocialMedia($record);

                        Notification::make()
                            ->title('E\'lon sotildi deb belgilandi!')
                            ->body('Ijtimoiy tarmoqlardagi postlar yangilandi va sotuvchi raqami yashirildi.')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListListings::route('/'),
            'create' => Pages\CreateListing::route('/create'),
            'edit' => Pages\EditListing::route('/{record}/edit'),
        ];
    }
}
