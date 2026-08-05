<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Foydalanuvchilar';

    protected static ?string $navigationLabel = 'Foydalanuvchilar';

    protected static ?string $modelLabel = 'foydalanuvchi';

    protected static ?string $pluralModelLabel = 'foydalanuvchilar';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Asosiy ma\'lumot')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Ism')
                            ->required(),
                        Forms\Components\TextInput::make('username')
                            ->label('Foydalanuvchi nomi')
                            ->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('phone')
                            ->label('Telefon raqami'),
                        Forms\Components\TextInput::make('password')
                            ->label('Parol')
                            ->password()
                            ->revealable()
                            ->dehydrateStateUsing(fn ($state) => filled($state) ? bcrypt($state) : null)
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $operation) => $operation === 'create')
                            ->helperText('Bo\'sh qoldirsangiz, joriy parol o\'zgarmaydi.'),
                        Forms\Components\Select::make('city_id')
                            ->label('Shahar')
                            ->relationship('city', 'name')
                            ->searchable()
                            ->preload(),
                    ]),
                Forms\Components\Section::make('Rollar')
                    ->schema([
                        Forms\Components\Select::make('roles')
                            ->label('Rollar')
                            ->relationship('roles', 'name')
                            ->multiple()
                            ->preload()
                            ->getOptionLabelFromRecordUsing(fn ($record) => config("access.roles.{$record->name}", $record->name)),
                    ]),
                Forms\Components\Section::make('Profil')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Textarea::make('bio')
                            ->label('Bio')
                            ->columnSpanFull(),
                        Forms\Components\Select::make('experience_level')
                            ->label('Tajriba darajasi')
                            ->options([
                                'beginner' => 'Yangi boshlovchi',
                                'intermediate' => 'O\'rtacha',
                                'advanced' => 'Tajribali',
                            ]),
                        Forms\Components\TextInput::make('height_cm')
                            ->label('Bo\'y (sm)')
                            ->numeric(),
                        Forms\Components\TextInput::make('budget_usd')
                            ->label('Byudjet ($)')
                            ->numeric(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Ism')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('roles.name')
                    ->label('Rollar')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => config("access.roles.{$state}", $state)),
                Tables\Columns\TextColumn::make('city.name')
                    ->label('Shahar')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Ro\'yxatdan o\'tgan')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('roles')
                    ->label('Rol')
                    ->relationship('roles', 'name')
                    ->getOptionLabelFromRecordUsing(fn ($record) => config("access.roles.{$record->name}", $record->name)),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn (User $record) => auth()->id() !== $record->id),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
