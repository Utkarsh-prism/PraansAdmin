<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LinkResource\Pages;
use App\Filament\Resources\LinkResource\RelationManagers;
use App\Models\Link;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;

class LinkResource extends Resource
{
    protected static ?string $model = Link::class;
    protected static ?string $navigationGroup = 'Blog';
    protected static ?string $navigationLabel = 'Links';
    protected static ?int $navigationSort = 4;
    protected static ?string $navigationIcon = 'heroicon-o-link';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()->schema([
                    TextInput::make('title')->required()->maxLength(255),
                    ColorPicker::make('color'),
                    Textarea::make('description')->columnSpanFull()->maxLength(65535),
                    TextInput::make('url')->required()->url()
                        ->suffixIcon('heroicon-o-arrow-top-right-on-square')
                        ->suffixIconColor('secondary')->maxLength(255),
                    FileUpload::make('image')->image()->directory('links')->maxSize(2048),
                ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image')->circular(),
                TextColumn::make('title')->sortable()->searchable(),
                TextColumn::make('url')->wrap(),
                TextColumn::make('color'),
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
            'index' => Pages\ListLinks::route('/'),
            'create' => Pages\CreateLink::route('/create'),
            'edit' => Pages\EditLink::route('/{record}/edit'),
        ];
    }
}
