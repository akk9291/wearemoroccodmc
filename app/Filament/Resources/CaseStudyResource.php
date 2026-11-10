<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CaseStudyResource\Pages;
use App\Filament\Support\ContentResource;
use App\Filament\Support\Fields;
use App\Models\CaseStudy;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;

class CaseStudyResource extends ContentResource
{
    protected static ?string $model = CaseStudy::class;

    protected static ?string $navigationIcon = 'heroicon-o-trophy';

    protected static ?int $navigationSort = 6;

    protected static ?string $navigationLabel = 'Case studies';

    protected static ?string $recordTitleAttribute = 'title';

    public static function publicUrl($record): ?string
    {
        return route('case-studies.show', $record);
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Case study')->columns(2)->schema([
                ...Fields::titleAndSlug(),
                Forms\Components\TextInput::make('client'),
                Forms\Components\TextInput::make('industry')->label('Service / industry'),
                Forms\Components\TextInput::make('group_size'),
                Forms\Components\TextInput::make('location'),
                Forms\Components\TextInput::make('year'),
                Fields::status(),
            ]),
            Forms\Components\Section::make('Card on /case-studies')->columns(2)->collapsible()->schema([
                Forms\Components\TextInput::make('card_kicker')->label('Kicker'),
                Forms\Components\TextInput::make('card_title')->label('Card title'),
                Forms\Components\Textarea::make('card_summary')->label('Card text')->rows(2),
                Forms\Components\Select::make('filter_service')->label('Filter')->options(['incentive' => 'Incentives', 'event' => 'Events', 'private' => 'Private']),
                Fields::image('card_image', 'Card image'),
                Forms\Components\TextInput::make('card_image_alt')->label('Card image alt text'),
            ]),
            Fields::hero(),
            Forms\Components\Section::make('Story')->collapsible()->schema([
                Forms\Components\Textarea::make('challenge')->rows(3),
                Forms\Components\Textarea::make('solution')->rows(3),
                Fields::lines('results', 'Results'),
                Fields::gallery(),
                Fields::code('body', 'Body (HTML)', 8),
                Fields::sections(),
            ]),
            Fields::seo(),
        ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return static::contentTable($table, [
            Fields::imageColumn('card_image', ''),
            Tables\Columns\TextColumn::make('title')->searchable()->sortable()->description(fn ($record) => $record->client),
            Tables\Columns\TextColumn::make('industry'),
            Fields::statusColumn(),
            static::updatedColumn(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCaseStudies::route('/'),
            'create' => Pages\CreateCaseStudy::route('/create'),
            'edit' => Pages\EditCaseStudy::route('/{record}/edit'),
        ];
    }
}
