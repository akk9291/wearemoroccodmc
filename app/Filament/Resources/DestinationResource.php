<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DestinationResource\Pages;
use App\Filament\Support\ContentResource;
use App\Filament\Support\Fields;
use App\Models\Destination;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Tables;
use Filament\Tables\Table;

class DestinationResource extends ContentResource
{
    protected static ?string $model = Destination::class;

    protected static ?string $navigationIcon = 'heroicon-o-globe-europe-africa';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'name';

    public static function publicUrl($record): ?string
    {
        return $record->has_page ? route('destinations.show', $record) : route('destinations.index').'#'.$record->slug;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Destination')->columns(2)->schema([
                ...Fields::titleAndSlug('name'),
                Forms\Components\TextInput::make('region'),
                Forms\Components\TextInput::make('best_season'),
                Fields::status(),
                Forms\Components\Toggle::make('has_page')->label('Has its own page')->live()->inline(false)
                    ->helperText('Off: the card links to the quote form.'),
            ]),
            Forms\Components\Section::make('"At a glance" card')->columns(2)->collapsible()->schema([
                Forms\Components\TextInput::make('best_for')->label('Best for')->columnSpanFull(),
                Forms\Components\Textarea::make('summary')->rows(2)->columnSpanFull(),
                Fields::image('card_image', 'Card image'),
                Forms\Components\TextInput::make('card_image_alt')->label('Card image alt text'),
            ]),
            Forms\Components\Section::make('Featured card (home + destinations)')->columns(2)->collapsible()->schema([
                Forms\Components\Toggle::make('is_featured')->label('Featured')->live()->columnSpanFull(),
                Forms\Components\TextInput::make('featured_name')->label('Card title'),
                Forms\Components\TextInput::make('tagline')->placeholder('The Red City'),
                Forms\Components\Textarea::make('intro')->label('Card text')->rows(2)->columnSpanFull(),
                Fields::image('featured_image', 'Featured image'),
                Forms\Components\TextInput::make('featured_image_alt')->label('Featured image alt text'),
                Forms\Components\TextInput::make('featured_order')->numeric()->label('Featured order'),
            ]),
            Fields::hero()->visible(fn (Get $get) => (bool) $get('has_page')),
            Forms\Components\Section::make('Page content')->visible(fn (Get $get) => (bool) $get('has_page'))->schema([
                Fields::lines('highlights', 'Highlights'),
                Fields::gallery(),
                Fields::sections(),
            ]),
            Fields::seo(),
        ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return static::contentTable($table, [
            Fields::imageColumn('card_image', ''),
            Tables\Columns\TextColumn::make('name')->searchable()->sortable()->description(fn ($record) => $record->best_for),
            Tables\Columns\IconColumn::make('has_page')->label('Page')->boolean(),
            Tables\Columns\IconColumn::make('is_featured')->label('Featured')->boolean(),
            Fields::statusColumn(),
            static::updatedColumn(),
        ], [
            Tables\Filters\TernaryFilter::make('is_featured')->label('Featured'),
            Tables\Filters\TernaryFilter::make('has_page')->label('Has page'),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDestinations::route('/'),
            'create' => Pages\CreateDestination::route('/create'),
            'edit' => Pages\EditDestination::route('/{record}/edit'),
        ];
    }
}
