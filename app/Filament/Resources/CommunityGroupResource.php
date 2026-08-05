<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CommunityGroupResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\Community\Models\CommunityGroup;

class CommunityGroupResource extends Resource
{
    protected static ?string $model = CommunityGroup::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'Hamjamiyat';

    protected static ?string $navigationLabel = 'Guruhlar';

    protected static ?string $modelLabel = 'guruh';

    protected static ?string $pluralModelLabel = 'guruhlar';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\SpatieMediaLibraryFileUpload::make('cover')
                    ->label('Muqova rasmi')
                    ->collection('cover')
                    ->image()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('name')
                    ->label('Nomi')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (string $operation, $state, Forms\Set $set) => $operation === 'create' ? $set('slug', str($state)->slug()) : null),
                Forms\Components\TextInput::make('slug')
                    ->label('Slug (URL)')
                    ->required()
                    ->unique(ignoreRecord: true),
                Forms\Components\Select::make('brand_id')
                    ->label('Brend (ixtiyoriy)')
                    ->relationship('brand', 'name')
                    ->searchable()
                    ->preload(),
                Forms\Components\Select::make('privacy')
                    ->label('Ko\'rinishi')
                    ->options(['public' => 'Ochiq', 'private' => 'Yopiq'])
                    ->default('public')
                    ->required(),
                Forms\Components\Textarea::make('description')
                    ->label('Tavsif')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nomi')
                    ->searchable(),
                Tables\Columns\TextColumn::make('brand.name')
                    ->label('Brend')
                    ->placeholder('—'),
                Tables\Columns\BadgeColumn::make('privacy')
                    ->label('Ko\'rinishi')
                    ->formatStateUsing(fn (string $state): string => $state === 'public' ? 'Ochiq' : 'Yopiq')
                    ->colors([
                        'success' => 'public',
                        'gray' => 'private',
                    ]),
                Tables\Columns\TextColumn::make('members_count')
                    ->label('A\'zolar')
                    ->counts('members'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Yaratilgan')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
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
            'index' => Pages\ManageCommunityGroups::route('/'),
        ];
    }
}
