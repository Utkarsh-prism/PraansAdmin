<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GazetteNortificationResource\Pages;
use App\Filament\Resources\GazetteNortificationResource\RelationManagers;
use App\Models\GazetteNortification;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Forms\Components\Card;
use App\Models\State;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Support\Str;

class GazetteNortificationResource extends Resource
{
    protected static ?string $model = GazetteNortification::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Compliance';
    protected static ?string $navigationLabel = 'Gazette Nortifications';
    protected static ?string $modelLabel = 'Gazette Nortification';
    protected static ?string $pluralModelLabel = 'Gazette Nortifications';

    public static function form(Form $form): Form
    {
        return $form
            
            ->schema([
            Forms\Components\Card::make('Details')
                ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255)
                    ->live(debounce: 500)
                    ->afterStateUpdated(function (Set $set, Get $get, ?string $state) {
                        // Only auto-fill if slug is empty (or was equal to old title’s slug)
                        $current = $get('slug');
                        if (blank($current) || $current === Str::slug($get('title'))) {
                            $set('slug', Str::slug((string) $state));
                        }
                    }),
                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->helperText('Auto-generated from Title. You can edit before saving.'),

                Forms\Components\TextInput::make('short_description')
                    ->default(null)
                    ->maxLength(555),
                    Forms\Components\Select::make('state')
                    ->label('State')
                    ->required()
                    ->options(fn () => State::query()
                        ->active()
                        ->ordered()
                        ->pluck('name', 'slug')   // value = slug, label = name
                        ->toArray()
                    )
                    ->searchable()
                    ->preload()
                    ->native(false),
                    Forms\Components\FileUpload::make('pdf_path')
                    ->label('Upload Docs')
                    ->directory('gazette-notification/')
                    ->preserveFilenames()
                    ->downloadable()
                    ->openable()
                    ->acceptedFileTypes([
                        'application/pdf',
                        'application/msword',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        'application/vnd.ms-excel',
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        'application/vnd.ms-powerpoint',
                        'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                        'image/*',
                    ]),
                    // ->columnSpan(2),    
                Forms\Components\DatePicker::make('updated_date'),
                Forms\Components\DatePicker::make('effective_date'),
                Forms\Components\RichEditor::make('description')
                    ->columnSpanFull(),
            ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('state')
                    ->searchable(),
                Tables\Columns\TextColumn::make('updated_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('effective_date')
                    ->date()
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
            'index' => Pages\ListGazetteNortifications::route('/'),
            'create' => Pages\CreateGazetteNortification::route('/create'),
            'edit' => Pages\EditGazetteNortification::route('/{record}/edit'),
        ];
    }
}
