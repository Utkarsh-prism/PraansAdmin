<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Filament\Resources\PostResource\RelationManagers;
use App\Models\Post;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\RichEditor;
use Illuminate\Support\Str;
use Filament\Forms\Set;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;


class PostResource extends Resource
{
    protected static ?string $model = Post::class;
    protected static ?string $navigationGroup = 'Blog';
    protected static ?string $navigationLabel = 'Posts';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationIcon = 'heroicon-o-document';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()->schema([
                    // TextInput::make('title')->required()->maxLength(255),
                    TextInput::make('title')
                    ->required()
                    ->maxLength(255)
                    ->live(debounce: 500) // type karte hi slug banega; prefer onBlur? use ->live(onBlur: true)
                    ->afterStateUpdated(function (Set $set, ?string $state) {
                      $set('slug', Str::slug((string) $state));
                       }),
                    // TextInput::make('slug')->required()->unique(ignoreRecord: true)->maxLength(255),

                    TextInput::make('slug')
    ->required()
    ->maxLength(255)
    // Edit screen pe current record ignore hoga:
    ->unique(ignorable: fn (?Model $record) => $record)
    // Ensure DB me slug hamesha slugified jaye:
    ->dehydrateStateUsing(fn (?string $state) => Str::slug((string) $state))
    ->helperText('Auto-generated from Title; you can tweak if needed.'),
                    RichEditor::make('content')->label('Desc')->columnSpanFull()->required(),
                    Textarea::make('short_description')->label('Sub-Desc')->columnSpanFull()->maxLength(5000),
                    Select::make('author_id')->relationship('author', 'name')->required()->searchable(),
                    Select::make('category_id')->relationship('category', 'name')->required()->searchable(),
                    DatePicker::make('published_date'),
                    TagsInput::make('tags')->label('Comments')->separator(','),
                    FileUpload::make('image')
    ->image()
    ->disk('public')
    ->directory('posts/images')
    ->visibility('public')
    ->acceptedFileTypes(['image/webp','image/jpeg','image/png'])
    ->maxSize(5120)
    ->openable()
    ->downloadable(),

FileUpload::make('meta_image')
    ->image()
    ->disk('public')
    ->directory('posts/meta')
    ->visibility('public')
    ->acceptedFileTypes(['image/webp','image/jpeg','image/png'])
    ->maxSize(5120)
    ->openable()
    ->downloadable(),

                ])->columns(2),
    
                Card::make()->schema([
                    TextInput::make('meta_title')->maxLength(255),
                    TextInput::make('meta_description')->maxLength(355),
                    TextInput::make('meta_keywords')->maxLength(355),
                ])->columns(2),
            
        ]);
        
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')->sortable()->searchable(),
                TextColumn::make('content')
                    ->formatStateUsing(fn ($state) => \Str::limit(strip_tags((string) $state), 70)),
                TextColumn::make('author.name')->label('Author')->sortable()->searchable(),
                TextColumn::make('category.name')->label('Category')->sortable()->searchable(),
                TextColumn::make('published_date')->date()->sortable(),
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
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}
