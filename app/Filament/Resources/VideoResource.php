<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VideoResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\Video\Models\Video;

class VideoResource extends Resource
{
    protected static ?string $model = Video::class;

    protected static ?string $navigationIcon = 'heroicon-o-video-camera';

    protected static ?string $navigationGroup = 'Kontent';

    protected static ?string $navigationLabel = 'Videolar';

    protected static ?string $modelLabel = 'video';

    protected static ?string $pluralModelLabel = 'videolar';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('category_id')
                    ->label('Kategoriya')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\Select::make('motorcycle_id')
                    ->label('Mototsikl (ixtiyoriy)')
                    ->relationship('motorcycle', 'name')
                    ->searchable()
                    ->preload(),
                Forms\Components\TextInput::make('title')
                    ->label('Sarlavha')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (string $operation, $state, Forms\Set $set) => $operation === 'create' ? $set('slug', str($state)->slug()) : null),
                Forms\Components\TextInput::make('slug')
                    ->label('Slug (URL)')
                    ->required()
                    ->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('url_or_path')
                    ->label('Video manzili (URL)')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('duration_seconds')
                    ->label('Davomiyligi (soniya)')
                    ->numeric(),
                Forms\Components\Toggle::make('is_ai_generated')
                    ->label('AI tomonidan yaratilgan'),
                Forms\Components\DateTimePicker::make('published_at')
                    ->label('Chop etilgan sana'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Sarlavha')
                    ->searchable(),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Kategoriya'),
                Tables\Columns\TextColumn::make('motorcycle.name')
                    ->label('Mototsikl'),
                Tables\Columns\IconColumn::make('is_ai_generated')
                    ->label('AI')
                    ->boolean(),
                Tables\Columns\TextColumn::make('duration_seconds')
                    ->label('Davomiyligi (s)')
                    ->numeric(),
                Tables\Columns\TextColumn::make('published_at')
                    ->label('Chop etilgan sana')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_ai_generated')
                    ->label('AI video'),
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
            'index' => Pages\ListVideos::route('/'),
            'create' => Pages\CreateVideo::route('/create'),
            'edit' => Pages\EditVideo::route('/{record}/edit'),
        ];
    }
}
