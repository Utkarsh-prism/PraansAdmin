<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ActRuleFormResource\Pages;
use App\Models\ActRuleForm;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Get;
use Closure;
use FilamentTiptapEditor\TiptapEditor;
use FilamentTiptapEditor\Enums\TiptapOutput;


class ActRuleFormResource extends Resource
{
    protected static ?string $model = ActRuleForm::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document';
    protected static ?string $navigationGroup = 'Compliance';
    protected static ?string $navigationLabel = 'Act  Rule & Form';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Card 1: Act/Rule meta
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\TextInput::make('title')
                        ->label('Title')
                        ->required()
                        ->maxLength(255)
                        ->reactive()
                        ->afterStateUpdated(function ($state, callable $set, callable $get) {
                            if (blank($get('slug')) && filled($state)) {
                                $set('slug', \Illuminate\Support\Str::slug($state));
                            }
                        })
                            ->columnSpan(1),
                        Forms\Components\TextInput::make('slug')
                            ->label('Slug')
                            ->helperText('Leave blank to auto-generate from Title.')
                            ->maxLength(255)
                            ->unique(table: \App\Models\ActRuleForm::class, column: 'slug', ignoreRecord: true)
                            ->dehydrated(fn ($state) => filled($state)),

                        Forms\Components\Select::make('state')
                            ->label('State / UT')
                            ->options(self::indianStates())
                            ->searchable()
                            ->required()
                            ->columnSpan(1),

                        Forms\Components\Textarea::make('short_description')
                            ->label('Short Description')
                            ->rows(3)
                            ->maxLength(5000) 
                            ->columnSpanFull(),

                        Forms\Components\FileUpload::make('upload_path')
                            ->label('Upload Docs (Act)')
                            ->directory('act-rule-forms/acts')
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
                            ])
                            ->columnSpan(1),

                        Forms\Components\FileUpload::make('form_image_path')
                            ->label('Upload Docs (Rule)')
                            ->directory('act-rule-forms/rule')
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
                            ])
                            ->columnSpan(1),

                            TiptapEditor::make('act_desc')
                            ->label('Act Desc')
                            ->profile('default')
                            ->tools([
                                'heading','bold','italic','underline','strike',
                                'color','highlight',
                                'align-left','align-center','align-right','align-justify',
                                'bullet-list','ordered-list','blockquote',
                                'link','hr','table','oembed',
                                'media',             
                                'code','code-block','details',
                                'undo','redo','source',
                              ])
                            ->output(TiptapOutput::Html)
                            ->columnSpanFull(),
                        
                        TiptapEditor::make('rule_desc')
                            ->label('Rule Desc')
                            ->tools([
                                'heading','bold','italic','underline','strike',
                                'color','highlight',
                                'align-left','align-center','align-right','align-justify',
                                'bullet-list','ordered-list','blockquote',
                                'link','hr','table','oembed',
                                'media',             
                                'code','code-block','details',
                                'undo','redo','source',
                              ])
                            ->profile('default')
                            ->output(TiptapOutput::Html)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                // Card 2: multi Forms (hasMany via Repeater)
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\Section::make('Forms')
                            ->description('Add all forms for this Act/Rule.')
                            ->schema([
                                Forms\Components\Repeater::make('forms')
                                ->relationship('forms')
                                ->label(false)
                                ->orderable('sort_order')
                                ->minItems(0)
                                ->collapsible()
                                ->grid(2)
                                ->itemLabel(fn (array $state): ?string =>
                                    ($state['form_no'] ?? 'Form') . ' – ' . ($state['title'] ?? '')
                                )
                                ->schema([
                                    // keep the child record id when editing
                                    Forms\Components\Hidden::make('id'),
                            
                                    Forms\Components\TextInput::make('form_no')
                                        ->label('Form No')
                                        ->maxLength(100)
                                        ->required()
                                        ->rule(function (Get $get) {
                                            // Inline validator to ensure unique Form No within the repeater
                                            return function (string $attribute, $value, Closure $fail) use ($get) {
                                                $items = collect($get('../../forms') ?? [])
                                                    ->pluck('form_no')
                                                    ->filter()
                                                    ->map(fn ($v) => trim((string) $v));
                            
                                                if ($items->count() !== $items->unique()->count()) {
                                                    $fail('Each Form No must be unique within this Act.');
                                                }
                                            };
                                        }),
                            
                                    Forms\Components\TextInput::make('title')
                                        ->label('Title')
                                        ->maxLength(255)
                                        ->required(),
                            
                                    Forms\Components\Textarea::make('short_desc')
                                        ->label('Description')
                                        ->rows(2)
                                        ->columnSpanFull(),
                            
                                    Forms\Components\FileUpload::make('pdf_path')
                                        ->label('PDF (required)')
                                        ->acceptedFileTypes(['application/pdf'])
                                        ->required()
                                        ->disk('public')
                                        ->directory('act-rule-forms/pdfs')
                                        ->preserveFilenames()
                                        ->downloadable()
                                        ->openable(),
                                ]),
                            
                    ]),
                ]),
                Forms\Components\Card::make()
                ->schema([
                    Forms\Components\Section::make('Counts')
                        ->description('Numbers that drive the UI badges.')
                        ->schema([
                            Forms\Components\TextInput::make('section_count')
                                ->label('Section Count')
                                ->numeric()
                                ->minValue(0)
                                ->step(1)
                                ->default(0)
                                ->required(),

                            Forms\Components\TextInput::make('rule_count')
                                ->label('Rule Count')
                                ->numeric()
                                ->minValue(0)
                                ->step(1)
                                ->default(0)
                                ->required(),

                            Forms\Components\TextInput::make('form_count')
                                ->label('Form Count')
                                ->numeric()
                                ->minValue(0)
                                ->step(1)
                                ->default(0)
                                ->required(),
                        ])
                        ->columns(3),
                ]),
        ]);
}

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->limit(40)
                    ->sortable(),
                Tables\Columns\TextColumn::make('state')
                    ->badge()
                    ->sortable()
                    ->searchable(),
                // Replaced child field with count
                Tables\Columns\TextColumn::make('forms_count')
                    ->counts('forms')
                    ->label('Forms')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->since()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('state')
                    ->options(self::indianStates())
                    ->label('State'),
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
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListActRuleForms::route('/'),
            'create' => Pages\CreateActRuleForm::route('/create'),
            'edit'   => Pages\EditActRuleForm::route('/{record}/edit'),
        ];
    }

    protected static function indianStates(): array
    {
        return [
            'All India' => 'All India',
            'Andaman & Nicobar Islands' => 'Andaman & Nicobar Islands',
            'Andhra Pradesh' => 'Andhra Pradesh',
            'Arunachal Pradesh' => 'Arunachal Pradesh',
            'Assam' => 'Assam',
            'Bihar' => 'Bihar',
            'Chandigarh' => 'Chandigarh',
            'Chhattisgarh' => 'Chhattisgarh',
            'Dadra & Nagar Haveli & Daman & Diu' => 'Dadra & Nagar Haveli & Daman & Diu',
            'Delhi' => 'Delhi',
            'Goa' => 'Goa',
            'Gujarat' => 'Gujarat',
            'Haryana' => 'Haryana',
            'Himachal Pradesh' => 'Himachal Pradesh',
            'Jammu & Kashmir' => 'Jammu & Kashmir',
            'Jharkhand' => 'Jharkhand',
            'Karnataka' => 'Karnataka',
            'Kerala' => 'Kerala',
            'Ladakh' => 'Ladakh',
            'Lakshadweep' => 'Lakshadweep',
            'Madhya Pradesh' => 'Madhya Pradesh',
            'Maharashtra' => 'Maharashtra',
            'Manipur' => 'Manipur',
            'Meghalaya' => 'Meghalaya',
            'Mizoram' => 'Mizoram',
            'Nagaland' => 'Nagaland',
            'Odisha' => 'Odisha',
            'Puducherry' => 'Puducherry',
            'Punjab' => 'Punjab',
            'Rajasthan' => 'Rajasthan',
            'Sikkim' => 'Sikkim',
            'Tamil Nadu' => 'Tamil Nadu',
            'Telangana' => 'Telangana',
            'Tripura' => 'Tripura',
            'Uttarakhand' => 'Uttarakhand',
            'Uttar Pradesh' => 'Uttar Pradesh',
            'West Bengal' => 'West Bengal',
        ];
    }
}
