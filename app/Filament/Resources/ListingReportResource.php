<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ListingReportResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\Market\Models\ListingReport;

class ListingReportResource extends Resource
{
    protected static ?string $model = ListingReport::class;

    protected static ?string $navigationIcon = 'heroicon-o-flag';

    protected static ?string $navigationGroup = 'Bozor';

    protected static ?string $navigationLabel = 'Shikoyatlar';

    protected static ?string $modelLabel = 'shikoyat';

    protected static ?string $pluralModelLabel = 'shikoyatlar';

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::where('status', 'pending')->count() ?: null;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('listing_id')
                    ->label('E\'lon')
                    ->relationship('listing', 'custom_title')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\Select::make('user_id')
                    ->label('Shikoyat qiluvchi')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\Textarea::make('reason')
                    ->label('Sabab')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\Select::make('status')
                    ->label('Holati')
                    ->options([
                        'pending' => 'Kutilmoqda',
                        'reviewed' => 'Ko\'rib chiqildi',
                    ])
                    ->default('pending')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('listing.custom_title')
                    ->label('E\'lon')
                    ->default('—')
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Shikoyat qiluvchi')
                    ->searchable(),
                Tables\Columns\TextColumn::make('reason')
                    ->label('Sabab')
                    ->limit(50),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Holati')
                    ->formatStateUsing(fn (string $state): string => $state === 'pending' ? 'Kutilmoqda' : 'Ko\'rib chiqildi')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'reviewed',
                    ]),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Yuborilgan')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Holati')
                    ->options([
                        'pending' => 'Kutilmoqda',
                        'reviewed' => 'Ko\'rib chiqildi',
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
            'index' => Pages\ManageListingReports::route('/'),
        ];
    }
}
