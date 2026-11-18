<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ShopRequestResource\Pages;
use App\Models\ShopRequest;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ShopRequestResource extends Resource
{
    protected static ?string $model = ShopRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-inbox-arrow-down';

    protected static ?string $navigationGroup = 'Savdo';

    protected static ?string $navigationLabel = 'Do\'kon arizalari';

    protected static ?string $modelLabel = 'do\'kon arizasi';

    protected static ?string $pluralModelLabel = 'do\'kon arizalari';

    public static function getNavigationBadge(): ?string
    {
        $count = ShopRequest::where('status', 'pending')->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('shop_name')
                    ->label('Do\'kon nomi')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\BadgeColumn::make('shop_type')
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
                    ->label('Ariza beruvchi')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Telefon')
                    ->searchable(),
                Tables\Columns\TextColumn::make('city.name')
                    ->label('Shahar'),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Holati')
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'pending' => 'Kutilmoqda',
                        'approved' => 'Tasdiqlangan',
                        'rejected' => 'Rad etilgan',
                        default => $state,
                    })
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'approved',
                        'danger' => 'rejected',
                    ]),
                Tables\Columns\IconColumn::make('has_location')
                    ->label('Lokatsiya')
                    ->state(fn (ShopRequest $record) => filled($record->lat) && filled($record->lng))
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Yuborilgan sana')
                    ->dateTime('d.m.Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Holati')
                    ->options([
                        'pending' => 'Kutilmoqda',
                        'approved' => 'Tasdiqlangan',
                        'rejected' => 'Rad etilgan',
                    ]),
                Tables\Filters\SelectFilter::make('shop_type')
                    ->label('Do\'kon turi')
                    ->options([
                        'moto' => 'Moto do\'kon',
                        'parts' => 'Ehtiyot qism do\'koni',
                        'both' => 'Ikkalasi',
                    ]),
            ])
            ->actions([
                // Tasdiqlash va Do'kon ochish
                Tables\Actions\Action::make('approve')
                    ->label('Tasdiqlash va Ochish')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (ShopRequest $record) => $record->isPending())
                    ->requiresConfirmation()
                    ->modalHeading('Do\'konni ochish va sotuvchini biriktirish')
                    ->modalDescription(fn (ShopRequest $record) => "Siz \"{$record->shop_name}\" do'konini tasdiqlab, uni sotuvchi {$record->user?->name}ga biriktirmoqchimisiz?")
                    ->form([
                        Forms\Components\TextInput::make('name')
                            ->label('Do\'kon nomi')
                            ->default(fn (ShopRequest $record) => $record->shop_name)
                            ->required(),
                        Forms\Components\Select::make('type')
                            ->label('Do\'kon turi')
                            ->options([
                                'moto' => 'Moto sotuvchi do\'kon',
                                'parts' => 'Ehtiyot qism do\'koni',
                                'both' => 'Ikkalasi',
                            ])
                            ->default(fn (ShopRequest $record) => $record->shop_type)
                            ->required(),
                        Forms\Components\TextInput::make('phone')
                            ->label('Telefon')
                            ->default(fn (ShopRequest $record) => $record->phone),
                        Forms\Components\TextInput::make('address')
                            ->label('Manzil')
                            ->default(fn (ShopRequest $record) => $record->address),
                        Forms\Components\TextInput::make('working_hours')
                            ->label('Ish vaqti')
                            ->placeholder('09:00 - 19:00, Har kuni'),
                    ])
                    ->action(function (ShopRequest $record, array $data): void {
                        $shop = $record->approve(auth()->user(), $data);

                        Notification::make()
                            ->title('Do\'kon ochildi va sotuvchiga biriktirildi!')
                            ->body("{$shop->name} do'koni yaratildi va {$record->user?->name} ga biriktirildi. Foydalanuvchiga qabul qilingani haqida SMS xabar yuborildi.")
                            ->success()
                            ->send();
                    }),

                // Rad etish
                Tables\Actions\Action::make('reject')
                    ->label('Rad etish')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (ShopRequest $record) => $record->isPending())
                    ->form([
                        Forms\Components\Textarea::make('reason')
                            ->label('Rad etish sababi')
                            ->required(),
                    ])
                    ->action(function (ShopRequest $record, array $data): void {
                        $record->reject(auth()->user(), $data['reason']);

                        Notification::make()
                            ->title('Ariza rad etildi')
                            ->warning()
                            ->send();
                    }),

                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListShopRequests::route('/'),
        ];
    }
}
