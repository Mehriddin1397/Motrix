<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AdPromotionResource\Pages;
use App\Models\AdPromotion;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AdPromotionResource extends Resource
{
    protected static ?string $model = AdPromotion::class;

    protected static ?string $navigationIcon = 'heroicon-o-megaphone';

    protected static ?string $navigationGroup = 'Reklama';

    protected static ?string $navigationLabel = 'Premium xizmatlar';

    protected static ?string $modelLabel = 'promo xizmat';

    protected static ?string $pluralModelLabel = 'premium xizmatlar';

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::where('status', 'pending_payment')->count() ?: null;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->label('Foydalanuvchi')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\Select::make('tier')
                    ->label('Tarif')
                    ->options([
                        'standard' => 'Oddiy',
                        'premium' => 'Premium',
                        'top' => 'TOP',
                        'vip' => 'VIP',
                    ])
                    ->required(),
                Forms\Components\Select::make('status')
                    ->label('Holati')
                    ->options([
                        'pending_payment' => 'To\'lov kutilmoqda',
                        'active' => 'Faol',
                        'expired' => 'Muddati o\'tgan',
                        'cancelled' => 'Bekor qilingan',
                    ])
                    ->default('pending_payment')
                    ->required(),
                Forms\Components\TextInput::make('price')
                    ->label('Narx')
                    ->numeric()
                    ->required(),
                Forms\Components\Select::make('currency')
                    ->label('Valyuta')
                    ->options(['USD' => 'USD', 'UZS' => 'UZS'])
                    ->default('USD')
                    ->required(),
                Forms\Components\DateTimePicker::make('starts_at')
                    ->label('Boshlanish sanasi'),
                Forms\Components\DateTimePicker::make('ends_at')
                    ->label('Tugash sanasi'),
                Forms\Components\TextInput::make('payment_reference')
                    ->label('To\'lov havolasi (ID)'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('promotable')
                    ->label('E\'lon/mahsulot')
                    ->getStateUsing(fn (AdPromotion $record) => $record->promotable?->name
                        ?? $record->promotable?->custom_title
                        ?? $record->promotable?->motorcycle?->name
                        ?? "#{$record->promotable_id}"),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Foydalanuvchi')
                    ->searchable(),
                Tables\Columns\BadgeColumn::make('tier')
                    ->label('Tarif')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'standard' => 'Oddiy',
                        'premium' => 'Premium',
                        'top' => 'TOP',
                        'vip' => 'VIP',
                        default => $state,
                    })
                    ->colors([
                        'gray' => 'standard',
                        'info' => 'premium',
                        'warning' => 'top',
                        'success' => 'vip',
                    ]),
                Tables\Columns\TextColumn::make('price')
                    ->label('Narx')
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Holati')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending_payment' => 'To\'lov kutilmoqda',
                        'active' => 'Faol',
                        'expired' => 'Muddati o\'tgan',
                        'cancelled' => 'Bekor qilingan',
                        default => $state,
                    })
                    ->colors([
                        'gray' => 'pending_payment',
                        'success' => 'active',
                        'warning' => 'expired',
                        'danger' => 'cancelled',
                    ]),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Yaratilgan')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Holati')
                    ->options([
                        'pending_payment' => 'To\'lov kutilmoqda',
                        'active' => 'Faol',
                        'expired' => 'Muddati o\'tgan',
                        'cancelled' => 'Bekor qilingan',
                    ]),
                Tables\Filters\SelectFilter::make('tier')
                    ->label('Tarif')
                    ->options([
                        'standard' => 'Oddiy',
                        'premium' => 'Premium',
                        'top' => 'TOP',
                        'vip' => 'VIP',
                    ]),
            ])
            ->actions([
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
            'index' => Pages\ManageAdPromotions::route('/'),
        ];
    }
}
