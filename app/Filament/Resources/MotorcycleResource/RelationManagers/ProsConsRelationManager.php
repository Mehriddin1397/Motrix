<?php

namespace App\Filament\Resources\MotorcycleResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProsConsRelationManager extends RelationManager
{
    protected static string $relationship = 'prosCons';

    protected static ?string $title = 'Kuchli va kuchsiz tomonlar';

    protected static ?string $modelLabel = 'jihat';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('type')
                    ->label('Turi')
                    ->options([
                        'pro' => 'Kuchli tomoni',
                        'con' => 'Kamchiligi',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('text')
                    ->label('Matn')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('text')
            ->columns([
                Tables\Columns\BadgeColumn::make('type')
                    ->label('Turi')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pro' => 'Kuchli tomoni',
                        'con' => 'Kamchiligi',
                        default => $state,
                    })
                    ->colors([
                        'success' => 'pro',
                        'danger' => 'con',
                    ]),
                Tables\Columns\TextColumn::make('text')
                    ->label('Matn'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
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
}
