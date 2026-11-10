<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceResource\Pages;
use App\Filament\Support\ContentResource;
use App\Filament\Support\Fields;
use App\Models\Service;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;

class ServiceResource extends ContentResource
{
    protected static ?string $model = Service::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'name';

    public static function publicUrl($record): ?string
    {
        return route('services.show', $record);
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Service')->columns(2)->schema([
                ...Fields::titleAndSlug('name'),
                Fields::status(),
            ]),
            Forms\Components\Section::make('Card on /services')->columns(2)->collapsible()->schema([
                Forms\Components\TextInput::make('card_kicker')->label('Kicker')->placeholder('Groups · 20 to 400+ guests'),
                Forms\Components\Textarea::make('card_summary')->label('Summary')->rows(2),
                Fields::image('card_image', 'Card image'),
                Forms\Components\TextInput::make('card_image_alt')->label('Card image alt text'),
            ]),
            Fields::hero(),
            Forms\Components\Section::make('Content')->schema([
                Fields::lines('highlights', 'Highlights'),
                Fields::sections(),
            ]),
            Fields::seo(),
        ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return static::contentTable($table, [
            Fields::imageColumn('card_image', ''),
            Tables\Columns\TextColumn::make('name')->searchable()->sortable()->description(fn ($record) => $record->card_kicker),
            Tables\Columns\TextColumn::make('slug')->color('gray')->toggleable(),
            Fields::statusColumn(),
            static::updatedColumn(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListServices::route('/'),
            'create' => Pages\CreateService::route('/create'),
            'edit' => Pages\EditService::route('/{record}/edit'),
        ];
    }
}
