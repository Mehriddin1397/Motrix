<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceProviderResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\ServiceCenter\Models\ServiceProvider;

class ServiceProviderResource extends Resource
{
    protected static ?string $model = ServiceProvider::class;

    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';

    protected static ?string $navigationGroup = 'Servislar';

    protected static ?string $navigationLabel = 'Xizmat markazlari';

    protected static ?string $modelLabel = 'xizmat markazi';

    protected static ?string $pluralModelLabel = 'xizmat markazlari';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\SpatieMediaLibraryFileUpload::make('logo')
                    ->label('Logotip')
                    ->collection('logo')
                    ->image()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('name')
                    ->label('Nomi')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\Select::make('user_id')
                    ->label('Egasi (foydalanuvchi)')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),
                Forms\Components\Select::make('category_id')
                    ->label('Kategoriya')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\Select::make('city_id')
                    ->label('Shahar')
                    ->relationship('city', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\TextInput::make('phone')
                    ->label('Telefon raqami'),
                Forms\Components\TextInput::make('address')
                    ->label('Manzil')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('lat')
                    ->label('Kenglik (lat)')
                    ->numeric(),
                Forms\Components\TextInput::make('lng')
                    ->label('Uzunlik (lng)')
                    ->numeric(),
                Forms\Components\Toggle::make('verified')
                    ->label('Tasdiqlangan'),
                Forms\Components\Textarea::make('description')
                    ->label('Tavsif')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\SpatieMediaLibraryImageColumn::make('logo')
                    ->label('Logotip')
                    ->collection('logo')
                    ->circular(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nomi')
                    ->searchable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Kategoriya'),
                Tables\Columns\TextColumn::make('city.name')
                    ->label('Shahar'),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Telefon'),
                Tables\Columns\IconColumn::make('verified')
                    ->label('Tasdiqlangan')
                    ->boolean(),
                Tables\Columns\TextColumn::make('rating_avg')
                    ->label('Reyting')
                    ->numeric(1),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('verified')
                    ->label('Tasdiqlangan'),
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
            'index' => Pages\ListServiceProviders::route('/'),
            'create' => Pages\CreateServiceProvider::route('/create'),
            'edit' => Pages\EditServiceProvider::route('/{record}/edit'),
        ];
    }
}
