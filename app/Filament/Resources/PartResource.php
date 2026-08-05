<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PartResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\Parts\Models\Part;

class PartResource extends Resource
{
    protected static ?string $model = Part::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationGroup = 'Ehtiyot qismlar';

    protected static ?string $navigationLabel = 'Mahsulotlar';

    protected static ?string $modelLabel = 'mahsulot';

    protected static ?string $pluralModelLabel = 'mahsulotlar';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\SpatieMediaLibraryFileUpload::make('images')
                    ->label('Rasmlar')
                    ->collection('images')
                    ->image()
                    ->multiple()
                    ->reorderable()
                    ->columnSpanFull(),
                Forms\Components\Select::make('seller_id')
                    ->label('Sotuvchi')
                    ->relationship('seller', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\Select::make('category_id')
                    ->label('Kategoriya')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\TextInput::make('name')
                    ->label('Nomi')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (string $operation, $state, Forms\Set $set) => $operation === 'create' ? $set('slug', str($state)->slug()) : null),
                Forms\Components\TextInput::make('slug')
                    ->label('Slug (URL)')
                    ->required()
                    ->unique(ignoreRecord: true),
                Forms\Components\Select::make('part_type')
                    ->label('Turi')
                    ->options(['oem' => 'OEM (original)', 'aftermarket' => 'Aftermarket'])
                    ->required(),
                Forms\Components\TextInput::make('part_number')
                    ->label('Ehtiyot qism raqami'),
                Forms\Components\TextInput::make('price')
                    ->label('Narx')
                    ->numeric()
                    ->prefix('$')
                    ->required(),
                Forms\Components\TextInput::make('stock_qty')
                    ->label('Ombordagi soni')
                    ->numeric()
                    ->default(0)
                    ->required(),
                Forms\Components\Select::make('condition')
                    ->label('Holati')
                    ->options(['new' => 'Yangi', 'used' => 'Ishlatilgan'])
                    ->required(),
                Forms\Components\Select::make('status')
                    ->label('Holati (e\'lon)')
                    ->options([
                        'pending' => 'Kutilmoqda',
                        'active' => 'Faol',
                        'sold_out' => 'Tugagan',
                        'rejected' => 'Rad etilgan',
                    ])
                    ->default('pending')
                    ->required(),
                Forms\Components\Textarea::make('description')
                    ->label('Tavsif')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\SpatieMediaLibraryImageColumn::make('images')
                    ->label('Rasm')
                    ->collection('images'),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nomi')
                    ->searchable(),
                Tables\Columns\TextColumn::make('seller.name')
                    ->label('Sotuvchi')
                    ->searchable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Kategoriya'),
                Tables\Columns\TextColumn::make('price')
                    ->label('Narx')
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('stock_qty')
                    ->label('Ombordagi soni')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Holati')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Kutilmoqda',
                        'active' => 'Faol',
                        'sold_out' => 'Tugagan',
                        'rejected' => 'Rad etilgan',
                        default => $state,
                    })
                    ->colors([
                        'gray' => 'pending',
                        'success' => 'active',
                        'info' => 'sold_out',
                        'danger' => 'rejected',
                    ]),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Yaratilgan')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Holati')
                    ->options([
                        'pending' => 'Kutilmoqda',
                        'active' => 'Faol',
                        'sold_out' => 'Tugagan',
                        'rejected' => 'Rad etilgan',
                    ]),
                Tables\Filters\SelectFilter::make('category_id')
                    ->label('Kategoriya')
                    ->relationship('category', 'name'),
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
            'index' => Pages\ListParts::route('/'),
            'create' => Pages\CreatePart::route('/create'),
            'edit' => Pages\EditPart::route('/{record}/edit'),
        ];
    }
}
