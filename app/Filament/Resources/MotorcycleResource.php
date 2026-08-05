<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MotorcycleResource\Pages;
use App\Filament\Resources\MotorcycleResource\RelationManagers\ProsConsRelationManager;
use Modules\Motorcycle\Models\Motorcycle;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MotorcycleResource extends Resource
{
    protected static ?string $model = Motorcycle::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Katalog';

    protected static ?string $navigationLabel = 'Mototsikllar';

    protected static ?string $modelLabel = 'mototsikl';

    protected static ?string $pluralModelLabel = 'mototsikllar';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Asosiy ma\'lumot')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('brand_id')
                            ->label('Brend')
                            ->relationship('brand', 'name')
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
                        Forms\Components\TextInput::make('generation')
                            ->label('Avlod'),
                        Forms\Components\TextInput::make('year_start')
                            ->label('Boshlanish yili')
                            ->numeric(),
                        Forms\Components\TextInput::make('year_end')
                            ->label('Tugash yili')
                            ->numeric(),
                        Forms\Components\Select::make('status')
                            ->label('Holati')
                            ->options([
                                'draft' => 'Qoralama',
                                'published' => 'Chop etilgan',
                            ])
                            ->default('draft')
                            ->required(),
                    ]),
                Forms\Components\Section::make('Rasmlar')
                    ->schema([
                        Forms\Components\SpatieMediaLibraryFileUpload::make('cover')
                            ->label('Muqova rasmi')
                            ->collection('cover')
                            ->image()
                            ->imageEditor()
                            ->columnSpanFull(),
                        Forms\Components\SpatieMediaLibraryFileUpload::make('gallery')
                            ->label('Galereya rasmlari')
                            ->collection('gallery')
                            ->image()
                            ->multiple()
                            ->reorderable()
                            ->appendFiles()
                            ->columnSpanFull(),
                    ]),
                Forms\Components\Section::make('Tavsif va tarix')
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->label('Tavsif')
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('history')
                            ->label('Tarix')
                            ->columnSpanFull(),
                    ]),
                Forms\Components\Section::make('Texnik xususiyatlar')
                    ->relationship('specification')
                    ->columns(3)
                    ->schema([
                        Forms\Components\TextInput::make('engine_type')->label('Dvigatel turi'),
                        Forms\Components\TextInput::make('displacement_cc')->label('Hajmi')->numeric()->suffix('cc'),
                        Forms\Components\TextInput::make('horsepower')->label('Quvvat')->numeric()->suffix('HP'),
                        Forms\Components\TextInput::make('torque_nm')->label('Aylanish momenti')->numeric()->suffix('Nm'),
                        Forms\Components\TextInput::make('top_speed_kmh')->label('Maksimal tezlik')->numeric()->suffix('km/h'),
                        Forms\Components\TextInput::make('weight_kg')->label('Og\'irligi')->numeric()->suffix('kg'),
                        Forms\Components\TextInput::make('fuel_capacity_l')->label('Yoqilg\'i bak hajmi')->numeric()->suffix('L'),
                        Forms\Components\TextInput::make('fuel_consumption_l_100km')->label('Yoqilg\'i sarfi')->numeric()->suffix('L/100km'),
                        Forms\Components\TextInput::make('transmission')->label('Uzatmalar qutisi'),
                        Forms\Components\TextInput::make('cooling_system')->label('Sovutish tizimi'),
                        Forms\Components\TextInput::make('price_usd_min')->label('Narx (min)')->numeric()->prefix('$'),
                        Forms\Components\TextInput::make('price_usd_max')->label('Narx (max)')->numeric()->prefix('$'),
                        Forms\Components\TextInput::make('reliability_score')->label('Ishonchlilik ko\'rsatkichi')->numeric()->step(0.1),
                        Forms\Components\Toggle::make('beginner_friendly')->label('Yangi boshlovchilar uchun qulay'),
                    ]),
                Forms\Components\Section::make('SEO')
                    ->schema([
                        Forms\Components\TextInput::make('meta_title')->label('Meta sarlavha'),
                        Forms\Components\Textarea::make('meta_description')
                            ->label('Meta tavsif')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\SpatieMediaLibraryImageColumn::make('cover')
                    ->label('Rasm')
                    ->collection('cover'),
                Tables\Columns\TextColumn::make('brand.name')
                    ->label('Brend')
                    ->sortable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Kategoriya')
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nomi')
                    ->searchable(),
                Tables\Columns\TextColumn::make('generation')
                    ->label('Avlod')
                    ->searchable(),
                Tables\Columns\TextColumn::make('year_start')
                    ->label('Boshlanish yili')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('year_end')
                    ->label('Tugash yili')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Holati')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft' => 'Qoralama',
                        'published' => 'Chop etilgan',
                        default => $state,
                    })
                    ->colors([
                        'gray' => 'draft',
                        'success' => 'published',
                    ]),
                Tables\Columns\TextColumn::make('views_count')
                    ->label('Ko\'rishlar soni')
                    ->numeric()
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
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Holati')
                    ->options([
                        'draft' => 'Qoralama',
                        'published' => 'Chop etilgan',
                    ]),
                Tables\Filters\SelectFilter::make('brand_id')
                    ->relationship('brand', 'name')
                    ->label('Brend'),
            ])
            ->actions([
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
            ProsConsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMotorcycles::route('/'),
            'create' => Pages\CreateMotorcycle::route('/create'),
            'edit' => Pages\EditMotorcycle::route('/{record}/edit'),
        ];
    }
}
