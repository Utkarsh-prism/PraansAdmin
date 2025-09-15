<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AuthorResource\Pages;
use App\Filament\Resources\AuthorResource\RelationManagers;
use App\Models\Author;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;

class AuthorResource extends Resource
{
    protected static ?string $model = Author::class;
    protected static ?string $navigationGroup = 'Blog';
    protected static ?string $navigationLabel = 'Authors';
    protected static ?int $navigationSort = 3;
    protected static ?string $navigationIcon = 'heroicon-o-user-circle';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()->schema([
                    TextInput::make('name')->required()->maxLength(255),
                    TextInput::make('email')->email()->unique(ignoreRecord: true)->required()->maxLength(255),
                    TextInput::make('github_handle')->suffixIcon('heroicon-m-globe-alt')->maxLength(255),
                    TextInput::make('twitter_handle')->suffixIcon('heroicon-m-sparkles')->maxLength(255),
                ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->sortable()->searchable(),
                TextColumn::make('email')->sortable()->searchable(),
                TextColumn::make('posts_count')->counts('posts')->label('Posts')->sortable(),
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAuthors::route('/'),
            'create' => Pages\CreateAuthor::route('/create'),
            'edit' => Pages\EditAuthor::route('/{record}/edit'),
        ];
    }
}
