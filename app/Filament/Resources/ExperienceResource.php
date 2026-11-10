<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExperienceResource\Pages;
use App\Filament\Support\ContentResource;
use App\Filament\Support\Fields;
use App\Models\Experience;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;

class ExperienceResource extends ContentResource
{
    protected static ?string $model = Experience::class;

    protected static ?string $navigationIcon = 'heroicon-o-sparkles';

    protected static ?int $navigationSort = 5;

    protected static ?string $recordTitleAttribute = 'title';

    /** Filter chips on /experiences, in the order they are written to data-cat. */
    public const CATEGORIES = ['gala' => 'Celebrations', 'desert' => 'Desert', 'culture' => 'Culture', 'food' => 'Food', 'adventure' => 'Adventure'];

    public static function publicUrl($record): ?string
    {
        return route('experiences');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make()->columns(2)->schema([
                ...Fields::titleAndSlug(),
                Forms\Components\TextInput::make('location')->label('Kicker / location')->placeholder('Agafay · 40 min from Marrakech'),
                Forms\Components\Select::make('destination_id')->relationship('destination', 'name')->searchable()->preload(),
                Forms\Components\CheckboxList::make('category')->label('Filter categories')->options(self::CATEGORIES)->columns(5)->columnSpanFull()
                    ->formatStateUsing(fn ($state) => is_array($state) ? $state : array_values(array_filter(explode(' ', (string) $state))))
                    // keep the stored order (e.g. "food culture"); newly ticked ones are appended
                    ->dehydrateStateUsing(fn ($state) => implode(' ', array_values(array_filter((array) $state, fn ($v) => isset(self::CATEGORIES[$v]))))),
                Forms\Components\Textarea::make('description')->rows(2)->columnSpanFull(),
                Fields::image('image'),
                Forms\Components\TextInput::make('image_alt')->label('Image alt text'),
                Fields::status(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return static::contentTable($table, [
            Fields::imageColumn('image', ''),
            Tables\Columns\TextColumn::make('title')->searchable()->sortable()->description(fn ($record) => $record->location),
            Tables\Columns\TextColumn::make('category')->badge()->separator(' '),
            Fields::statusColumn(),
            static::updatedColumn(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListExperiences::route('/'),
            'create' => Pages\CreateExperience::route('/create'),
            'edit' => Pages\EditExperience::route('/{record}/edit'),
        ];
    }
}
