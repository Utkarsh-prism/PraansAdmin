<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StateResource\Pages;
use App\Filament\Resources\StateResource\RelationManagers;
use App\Models\State;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StateResource extends Resource
{
    protected static ?string $model = State::class;
    protected static ?string $navigationGroup = 'Compliance';
    protected static ?string $navigationIcon = 'heroicon-o-map-pin';
    protected static ?string $navigationLabel = 'States';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('State / Option')
                        ->required()
                        ->maxLength(100)
                        ->reactive()
                        ->afterStateUpdated(fn ($state, $set, $get) =>
                            blank($get('slug')) && filled($state)
                                ? $set('slug', \Illuminate\Support\Str::slug($state))
                                : null
                        ),

                    Forms\Components\TextInput::make('slug')
                        ->helperText('Leave blank to auto-generate from Name')
                        ->unique(ignoreRecord: true)
                        ->dehydrated(fn ($state) => filled($state)),

                   

                    Forms\Components\TextInput::make('sort_order')
                        ->label('Sort Order')
                        ->numeric()
                        ->default(100),
                    Forms\Components\Toggle::make('is_active')
                        ->label('Active')
                        ->default(true),    
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('slug')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('sort_order')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStates::route('/'),
            'create' => Pages\CreateState::route('/create'),
            'edit' => Pages\EditState::route('/{record}/edit'),
        ];
    }
}
