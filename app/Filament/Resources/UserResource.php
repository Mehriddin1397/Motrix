<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
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
                Forms\Components\Section::make('Rollar va Ruxsatlar')
                    ->description('Foydalanuvchiga tizimdagi vazifasiga ko\'ra rollar biriktiring. Administrator roli tizimni to\'liq boshqarish imkonini beradi.')
                    ->schema([
                        Forms\Components\Select::make('roles')
                            ->label('Rollar')
                            ->relationship('roles', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->getOptionLabelFromRecordUsing(fn ($record) => match ($record->name) {
                                'administrator' => '👑 Administrator (Barcha huquqlar va admin panel)',
                                'moderator' => '🛡️ Moderator (E\'lonlar va kontent nazorati)',
                                'moto-seller' => '🏍️ Moto sotuvchi',
                                'parts-seller' => '⚙️ Ehtiyot qismlar sotuvchisi',
                                'user' => '👤 Oddiy foydalanuvchi',
                                default => config("access.roles.{$record->name}", $record->name),
                            })
                            ->helperText('Administrator roli tanlansa, ushbu foydalanuvchi admin panelga kirish va barcha ma\'lumotlarni boshqarish huquqiga ega bo\'ladi.'),
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
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'administrator' => '👑 Administrator',
                        'moderator' => '🛡️ Moderator',
                        'moto-seller' => '🏍️ Moto sotuvchi',
                        'parts-seller' => '⚙️ Qismlar sotuvchi',
                        'user' => 'Oddiy foydalanuvchi',
                        default => config("access.roles.{$state}", $state),
                    })
                    ->colors([
                        'danger' => 'administrator',
                        'warning' => 'moderator',
                        'success' => 'moto-seller',
                        'info' => 'parts-seller',
                        'gray' => 'user',
                    ]),
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
                Tables\Actions\Action::make('change_roles')
                    ->label('Rolni o\'zgartirish')
                    ->icon('heroicon-o-shield-check')
                    ->color('warning')
                    ->modalHeading(fn (User $record) => "{$record->name} — Rolni o'zgartirish")
                    ->modalDescription('Foydalanuvchiga bir yoki bir nechta rollarni belgilang. "Administrator" roli admin panelga to\'liq kirish huquqini beradi.')
                    ->visible(fn () => auth()->user()?->hasRole('administrator') || auth()->user()?->can('users.roles.manage') || auth()->user()?->can('users.manage'))
                    ->mountUsing(fn (Forms\ComponentContainer $form, User $record) => $form->fill([
                        'roles' => $record->roles->pluck('id')->toArray(),
                    ]))
                    ->form([
                        Forms\Components\Select::make('roles')
                            ->label('Foydalanuvchi rollari')
                            ->multiple()
                            ->relationship('roles', 'name')
                            ->preload()
                            ->searchable()
                            ->getOptionLabelFromRecordUsing(fn ($record) => match ($record->name) {
                                'administrator' => '👑 Administrator (Barcha huquqlar va admin panel)',
                                'moderator' => '🛡️ Moderator (E\'lonlar va kontent nazorati)',
                                'moto-seller' => '🏍️ Moto sotuvchi',
                                'parts-seller' => '⚙️ Ehtiyot qismlar sotuvchisi',
                                'user' => '👤 Oddiy foydalanuvchi',
                                default => config("access.roles.{$record->name}", $record->name),
                            })
                            ->required()
                            ->helperText('Diqqat: "Administrator" roli tizim ustidan to\'liq nazorat va admin panelga kirish imkonini beradi.'),
                    ])
                    ->action(function (User $record, array $data): void {
                        $record->roles()->sync($data['roles'] ?? []);

                        Notification::make()
                            ->title('Rollar muvaffaqiyatli yangilandi')
                            ->body("{$record->name} foydalanuvchisining rollari saqlandi.")
                            ->success()
                            ->send();
                    }),
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
