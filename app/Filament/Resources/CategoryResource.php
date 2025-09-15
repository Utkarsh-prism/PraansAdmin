<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Filament\Resources\CategoryResource\RelationManagers;
use App\Models\Category;
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

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;
    protected static ?string $navigationGroup = 'Blog';
    protected static ?string $navigationLabel = 'Categories';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationIcon = 'heroicon-o-tag';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
           Card::make()->schema([
                TextInput::make('name')->unique(ignoreRecord: true)->required()->maxLength(255),
                TextInput::make('slug')->unique(ignoreRecord: true)->required()->maxLength(255),
            ])->columns(2)
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
            TextColumn::make('name')->sortable()->searchable(),
            TextColumn::make('slug')->sortable()->searchable(),
            TextColumn::make('posts_count')->counts('posts')->label('Posts')->sortable(),
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
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
