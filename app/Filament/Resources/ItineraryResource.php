<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ItineraryResource\Pages;
use App\Filament\Support\ContentResource;
use App\Filament\Support\Fields;
use App\Models\Itinerary;
use Filament\Forms;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;

class ItineraryResource extends ContentResource
{
    protected static ?string $model = Itinerary::class;

    protected static ?string $navigationIcon = 'heroicon-o-map';

    protected static ?int $navigationSort = 4;

    protected static ?string $recordTitleAttribute = 'title';

    public static function publicUrl($record): ?string
    {
        return route('itineraries.show', $record);
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Tabs::make('Itinerary')->persistTabInQueryString()->columnSpanFull()->tabs([
                Tabs\Tab::make('Overview')->icon('heroicon-o-information-circle')->schema([
                    Forms\Components\Grid::make(2)->schema([
                        ...Fields::titleAndSlug(),
                        Fields::status(),
                        Forms\Components\Toggle::make('is_signature')->label('Signature journey')->inline(false)
                            ->helperText('Signature journeys appear on the home page and in the top row.'),
                        Forms\Components\TextInput::make('number')->maxLength(10)->placeholder('01'),
                        Forms\Components\TextInput::make('tagline')->placeholder('The Elegant Introduction'),
                        Forms\Components\TextInput::make('days')->numeric()->minValue(0),
                        Forms\Components\TextInput::make('nights')->numeric()->minValue(0),
                        Forms\Components\TextInput::make('duration_label')->required()->placeholder('8 days / 7 nights'),
                        Forms\Components\TextInput::make('theme'),
                        Forms\Components\TextInput::make('price_from')->numeric()->prefix('from'),
                        Forms\Components\Select::make('currency')->options(['EUR' => 'EUR', 'USD' => 'USD', 'MAD' => 'MAD', 'GBP' => 'GBP'])->default('EUR'),
                        Forms\Components\TextInput::make('route')->label('Route (hero)')->columnSpanFull(),
                        Forms\Components\TextInput::make('card_route')->label('Route (cards)')->columnSpanFull(),
                        Forms\Components\Textarea::make('summary')->label('Summary (hero lead)')->rows(3)->columnSpanFull(),
                        Forms\Components\Textarea::make('character')->label('Journey character')->rows(2)->columnSpanFull(),
                        Forms\Components\Select::make('destinations')->relationship('destinations', 'name')->multiple()->preload()->columnSpanFull(),
                        Forms\Components\TextInput::make('inquiry_key')->label('Inquiry form value')->placeholder('grand-morocco')
                            ->helperText('Pre-selects "Interest" in the inquiry form.'),
                    ]),
                    Forms\Components\Fieldset::make('Card')->schema([
                        Fields::image('card_image', 'Card image'),
                        Forms\Components\TextInput::make('card_image_alt')->label('Card image alt text'),
                    ]),
                    Forms\Components\Fieldset::make('Filters on /itineraries')->columns(3)->schema([
                        Forms\Components\TextInput::make('filter_type')->label('Type')->helperText('culture desert private incentive'),
                        Forms\Components\TextInput::make('filter_dest')->label('Destinations')->helperText('marrakech fes sahara atlas'),
                        Forms\Components\Select::make('filter_len')->label('Length')->options(['short' => 'Up to 5 days', 'week' => 'About a week', 'long' => '10 days +']),
                    ]),
                ]),
                Tabs\Tab::make('Hero')->icon('heroicon-o-photo')->schema([
                    Fields::image('hero_image', 'Hero image'),
                    Forms\Components\TextInput::make('hero_image_alt')->label('Hero image alt text'),
                    Forms\Components\Repeater::make('hero_crumbs')->label('Breadcrumbs')->schema([
                        Forms\Components\TextInput::make('label')->required(),
                        Forms\Components\TextInput::make('url'),
                    ])->columns(2)->defaultItems(0)->reorderable(),
                    Forms\Components\TextInput::make('hero_eyebrow')->label('Eyebrow')->placeholder('03 · Signature journey'),
                    Forms\Components\Textarea::make('hero_title')->label('Heading (H1, HTML allowed)')->rows(2),
                ]),
                Tabs\Tab::make('Days')->icon('heroicon-o-calendar-days')->schema([
                    Forms\Components\Repeater::make('itineraryDays')->label('Day by day')
                        ->relationship()->orderColumn('day_no')->reorderable()->collapsible()->cloneable()
                        ->itemLabel(fn (array $state) => ($state['title'] ?? null) ? strip_tags($state['title']) : null)
                        ->schema([
                            Forms\Components\TextInput::make('title')->required()->columnSpanFull(),
                            Forms\Components\RichEditor::make('description')->columnSpanFull()
                                ->toolbarButtons(['bold', 'italic', 'link', 'undo', 'redo']),
                            Fields::image('image', 'Day image'),
                            Forms\Components\TextInput::make('image_alt')->label('Image alt text'),
                            Forms\Components\TextInput::make('overnight'),
                            Forms\Components\TextInput::make('meals')->placeholder('B, L, D'),
                        ])->columns(2)
                        ->helperText('Drag to reorder; day numbers follow the order.'),
                ]),
                Tabs\Tab::make('Inclusions')->icon('heroicon-o-check-circle')->schema([
                    Fields::lines('highlights', 'Highlights (home page journey tabs)'),
                    Fields::lines('route_stops', 'Route stops (side box)'),
                    Fields::lines('includes', 'Typically included'),
                    Fields::lines('excludes', 'Typically excluded'),
                ]),
                Tabs\Tab::make('Gallery')->icon('heroicon-o-squares-2x2')->schema([
                    Forms\Components\Section::make('Route map')->description('Shown beside the day-by-day route. Use a wide 3:2 image.')->schema([
                        Fields::image('route_map', 'Route map'),
                        Forms\Components\TextInput::make('route_map_alt')->label('Route map alt text')->maxLength(255),
                    ])->collapsible(),
                    Fields::gallery(),
                ]),
                Tabs\Tab::make('Closing banner')->icon('heroicon-o-megaphone')->schema([
                    Forms\Components\Group::make(Fields::ctaFields())->statePath('final_cta'),
                ]),
                Tabs\Tab::make('SEO')->icon('heroicon-o-magnifying-glass')->schema([
                    Forms\Components\TextInput::make('meta_title')->maxLength(255),
                    Forms\Components\Textarea::make('meta_description')->rows(3),
                    Fields::image('og_image', 'Social share image (og:image)'),
                ]),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return static::contentTable($table, [
            Fields::imageColumn('card_image', ''),
            Tables\Columns\TextColumn::make('title')->searchable()->sortable()->description(fn ($record) => $record->card_route),
            Tables\Columns\TextColumn::make('duration_label')->label('Length'),
            Tables\Columns\IconColumn::make('is_signature')->label('Signature')->boolean(),
            Tables\Columns\TextColumn::make('itinerary_days_count')->counts('itineraryDays')->label('Days'),
            Fields::statusColumn(),
            static::updatedColumn(),
        ], [
            Tables\Filters\TernaryFilter::make('is_signature')->label('Signature'),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListItineraries::route('/'),
            'create' => Pages\CreateItinerary::route('/create'),
            'edit' => Pages\EditItinerary::route('/{record}/edit'),
        ];
    }
}
