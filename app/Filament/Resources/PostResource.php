<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\Community\Models\Post;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationGroup = 'Hamjamiyat';

    protected static ?string $navigationLabel = 'Postlar';

    protected static ?string $modelLabel = 'post';

    protected static ?string $pluralModelLabel = 'postlar';

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::where('status', 'reported')->count() ?: null;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->label('Muallif')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\Select::make('group_id')
                    ->label('Guruh (ixtiyoriy)')
                    ->relationship('group', 'name')
                    ->searchable()
                    ->preload(),
                Forms\Components\Select::make('motorcycle_id')
                    ->label('Mototsikl (ixtiyoriy)')
                    ->relationship('motorcycle', 'name')
                    ->searchable()
                    ->preload(),
                Forms\Components\Select::make('type')
                    ->label('Turi')
                    ->options(['post' => 'Post', 'question' => 'Savol'])
                    ->default('post')
                    ->required(),
                Forms\Components\Textarea::make('body')
                    ->label('Matn')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\Select::make('status')
                    ->label('Holati')
                    ->options([
                        'published' => 'Chop etilgan',
                        'hidden' => 'Yashirilgan',
                        'reported' => 'Shikoyat qilingan',
                    ])
                    ->default('published')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Muallif')
                    ->searchable(),
                Tables\Columns\TextColumn::make('group.name')
                    ->label('Guruh')
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('body')
                    ->label('Matn')
                    ->limit(50),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Holati')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'published' => 'Chop etilgan',
                        'hidden' => 'Yashirilgan',
                        'reported' => 'Shikoyat qilingan',
                        default => $state,
                    })
                    ->colors([
                        'success' => 'published',
                        'gray' => 'hidden',
                        'danger' => 'reported',
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
                        'published' => 'Chop etilgan',
                        'hidden' => 'Yashirilgan',
                        'reported' => 'Shikoyat qilingan',
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
            'index' => Pages\ManagePosts::route('/'),
        ];
    }
}
