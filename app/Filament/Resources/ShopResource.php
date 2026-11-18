<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ShopResource\Pages;
use App\Models\Shop;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ShopResource extends Resource
{
    protected static ?string $model = Shop::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';

    protected static ?string $navigationGroup = 'Savdo';

    protected static ?string $navigationLabel = 'Do\'konlar';

    protected static ?string $modelLabel = 'do\'kon';

    protected static ?string $pluralModelLabel = 'do\'konlar';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Asosiy ma\'lumotlar')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Do\'kon nomi')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('type')
                            ->label('Do\'kon turi')
                            ->options([
                                'moto' => 'Moto sotuvchi do\'kon',
                                'parts' => 'Ehtiyot qism do\'koni',
                                'both' => 'Moto va ehtiyot qism do\'koni',
                            ])
                            ->default('moto')
                            ->required(),
                        Forms\Components\Select::make('user_id')
                            ->label('Biriktirilgan sotuvchi (Egasi)')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->helperText('Admin do\'konga sotuvchini biriktiradi.'),
                        Forms\Components\Select::make('city_id')
                            ->label('Shahar / Viloyat')
                            ->relationship('city', 'name')
                            ->searchable()
                            ->preload(),
                        Forms\Components\TextInput::make('phone')
                            ->label('Telefon raqami')
                            ->tel(),
                        Forms\Components\TextInput::make('working_hours')
                            ->label('Ish vaqti')
                            ->placeholder('09:00 - 19:00, Har kuni'),
                    ]),

                Forms\Components\Section::make('Manzil va Lokatsiya')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('address')
                            ->label('Aniq manzil')
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('lat')
                            ->label('Kenglik (Latitude)')
                            ->numeric()
                            ->helperText('Masalan: 41.311081'),
                        Forms\Components\TextInput::make('lng')
                            ->label('Uzunlik (Longitude)')
                            ->numeric()
                            ->helperText('Masalan: 69.240562'),
                    ]),

                Forms\Components\Section::make('Qo\'shimcha ma\'lumotlar')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('telegram')
                            ->label('Telegram'),
                        Forms\Components\TextInput::make('instagram')
                            ->label('Instagram'),
                        Forms\Components\Textarea::make('description')
                            ->label('Tavsif')
                            ->columnSpanFull(),
                        Forms\Components\Toggle::make('is_verified')
                            ->label('Tasdiqlangan do\'kon')
                            ->default(true),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Faol do\'kon')
                            ->default(true),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Do\'kon nomi')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\BadgeColumn::make('type')
                    ->label('Turi')
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'moto' => 'Moto do\'kon',
                        'parts' => 'Ehtiyot qism',
                        'both' => 'Ikkalasi',
                        default => $state,
                    })
                    ->colors([
                        'warning' => 'moto',
                        'primary' => 'parts',
                        'success' => 'both',
                    ]),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Biriktirilgan sotuvchi')
                    ->searchable(),
                Tables\Columns\TextColumn::make('city.name')
                    ->label('Shahar'),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Telefon'),
                Tables\Columns\IconColumn::make('has_location')
                    ->label('Lokatsiya')
                    ->state(fn (Shop $record) => $record->hasCoordinates())
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_verified')
                    ->label('Tasdiqlangan')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Faol')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Qo\'shilgan')
                    ->dateTime('d.m.Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Do\'kon turi')
                    ->options([
                        'moto' => 'Moto sotuvchi do\'kon',
                        'parts' => 'Ehtiyot qism do\'koni',
                        'both' => 'Ikkalasi',
                    ]),
                Tables\Filters\TernaryFilter::make('is_verified')
                    ->label('Tasdiqlangan'),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Faol'),
            ])
            ->actions([
                Tables\Actions\Action::make('navigate')
                    ->label('Navigatsiya')
                    ->icon('heroicon-o-map-pin')
                    ->color('warning')
                    ->url(fn (Shop $record) => $record->getYandexNaviUrl(), shouldOpenInNewTab: true)
                    ->visible(fn (Shop $record) => $record->hasCoordinates()),
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
            'index' => Pages\ListShops::route('/'),
            'create' => Pages\CreateShop::route('/create'),
            'edit' => Pages\EditShop::route('/{record}/edit'),
        ];
    }
}
