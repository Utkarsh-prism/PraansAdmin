<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HolidayResource\Pages;
use App\Filament\Resources\HolidayResource\RelationManagers;
use App\Models\Holiday;
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
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Card;
use Filament\Forms\Set;
use Illuminate\Database\Eloquent\Model;
use App\Models\State;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Actions\ActionGroup;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Filament\Forms\Get;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Hidden;


class HolidayResource extends Resource
{
    protected static ?string $model = Holiday::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationGroup = 'Compliance';
    // protected static ?int $navigationSort = 20;
    protected static ?string $modelLabel = 'Holiday';
    protected static ?string $pluralModelLabel = 'Holidays';

    public static function form(Form $form): Form
    {
        // Month and Day static options
        $months = [
            'January','February','March','April','May','June',
            'July','August','September','October','November','December'
        ];

        $days = [
            'Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'
        ];

        return $form
            ->schema([
                Forms\Components\Section::make('Basic Info')
                    ->columns(2)
                    ->schema([
                        // State: assuming you store state name as string (not FK).
                        Forms\Components\Select::make('state')
                            ->label('State')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->options(fn () => class_exists(State::class)
                                ? State::query()->orderBy('name')->pluck('name', 'name')->toArray()
                                : [])
                            ->hint(fn () => class_exists(State::class) ? null : 'Tip: No State model found, options empty.'),
                        
                        Forms\Components\TextInput::make('title')
                            ->label('Title')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Set $set, ?string $state, ?Holiday $record) {
                                if ($state) {
                                    // Show live slug but ensure uniqueness on save in model
                                    $set('slug', Str::slug($state));
                                }
                            }),

                        Forms\Components\TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->helperText('Auto-generated from title. You can edit if needed.'),

                        Forms\Components\RichEditor::make('short_desc')
                            ->label('Short Desc')
                            ->maxLength(2000)
                            ->columnSpanFull(),
                    ]),

                    Forms\Components\Section::make('Holiday Details')
                    ->schema([
                        Repeater::make('details')
                            ->relationship('details')        // << hasMany(HolidayDetail::class)
                            ->label(false)
                            ->orderable('sort_order')        // same as Forms repeater
                            ->minItems(0)
                            ->collapsible()
                            ->grid(2)                        // 2-column layout inside each item
                            ->itemLabel(function (array $state): ?string {
                                $name = trim($state['holiday_name'] ?? 'Holiday');
                                $date = $state['date'] ?? null;
                            
                                if ($date instanceof \Carbon\Carbon) {
                                    $formatted = $date->format('d-m-Y');
                                } elseif (is_string($date) && $date !== '') {
                                    try {
                                        $formatted = \Carbon\Carbon::parse($date)->format('d-m-Y');
                                    } catch (\Throwable $e) {
                                        $formatted = $date; // fallback
                                    }
                                } else {
                                    $formatted = null;
                                }
                            
                                return $formatted ? "{$name} – {$formatted}" : $name;
                            })                            
                            ->schema([
                                Hidden::make('id'),
                
                                TextInput::make('holiday_name')
                                    ->label('Holiday Name')
                                    ->placeholder('Diwali, Holi, etc.')
                                    ->required()
                                    ->maxLength(120)
                                    ->columnSpan(1),
                
                                Select::make('type')
                                    ->label('Type')
                                    ->options([
                                        'Regional' => 'Regional',
                                        'National' => 'National',
                                        'Optional' => 'Optional',
                                    ])
                                    ->required()
                                    ->native(false)
                                    ->columnSpan(1),
                
                                DatePicker::make('date')
                                    ->label('Date')
                                    ->native(false)
                                    ->displayFormat('d-m-Y')    // UI
                                    ->format('Y-m-d')           // DB
                                    ->firstDayOfWeek(1)
                                    ->required()
                                    ->closeOnDateSelection()
                                    ->live()
                                    ->afterStateUpdated(function ($state, \Filament\Forms\Set $set) {
                                        if (!$state) return;
                                        $c = \Carbon\Carbon::parse($state);
                                        $set('day', $c->format('l'));
                                        $set('month', $c->format('F'));
                                    })
                                    ->columnSpan(1),
                
                                Select::make('day')
                                    ->label('Day')
                                    ->options(array_combine($days, $days))
                                    ->disabled()    
                                    ->dehydrated()  
                                    ->required()
                                    ->columnSpan(1),
                
                                Select::make('month')
                                    ->label('Month')
                                    ->options(array_combine($months, $months))
                                    ->disabled()
                                    ->dehydrated()
                                    ->required()
                                    ->columnSpan(1),
                            ])->columnSpanFull(),  // inside each item,
                    ]),                
                Forms\Components\Section::make('Attachments')
                    ->schema([
                        Forms\Components\FileUpload::make('holiday_pdf')
                            ->label('Holiday PDF')
                            ->acceptedFileTypes(['application/pdf'])
                            ->maxSize(10240) 
                            ->disk('public')
                            ->directory('holidays/pdfs')
                            ->downloadable()
                            ->openable()
                            ->visibility('public')
                            ->getUploadedFileNameForStorageUsing(function (TemporaryUploadedFile $file, Get $get): string {
                                $base = Str::slug($get('title') ?: pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
                                return $base . '-' . now()->format('YmdHis') . '.' . $file->getClientOriginalExtension();
                            }),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('state')->label('State')->searchable()->sortable(),
                TextColumn::make('title')->label('Title')->searchable()->sortable()->limit(50),
                // TextColumn::make('slug')->label('Slug')->searchable()->sortable()->limit(50),
                TextColumn::make('details_count')->counts('details')->label('No. of Holidays')->sortable(),
                // TextColumn::make('created_at')->label('Created')->dateTime('d-M-Y h:i A')->sortable(),
                // TextColumn::make('updated_at')->label('Updated')->dateTime('d-M-Y h:i A')->sortable(),
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
            'index' => Pages\ListHolidays::route('/'),
            'create' => Pages\CreateHoliday::route('/create'),
            'edit' => Pages\EditHoliday::route('/{record}/edit'),
        ];
    }
}
