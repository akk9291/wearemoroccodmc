<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PageResource\Pages;
use App\Filament\Support\ContentResource;
use App\Filament\Support\Fields;
use App\Models\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Route;

class PageResource extends ContentResource
{
    protected static ?string $model = Page::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'title';

    /** A page's slug is the name of its route (about, faq...) or of its section index (services.index...). */
    public static function publicUrl($record): ?string
    {
        foreach ([$record->slug, $record->slug.'.index'] as $name) {
            if (Route::has($name)) {
                return route($name);
            }
        }

        return null;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Page')->columns(2)->schema([
                ...Fields::titleAndSlug(),
                Forms\Components\Select::make('template')->options(['default' => 'Default', 'home' => 'Home'])->default('default')->required(),
                Fields::status(),
            ]),
            Fields::hero(),
            Forms\Components\Section::make('Content')->schema([Fields::sections()]),
            Fields::seo(),
        ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return static::contentTable($table, [
            Tables\Columns\TextColumn::make('title')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('slug')->searchable()->color('gray'),
            Tables\Columns\TextColumn::make('template')->badge()->color('gray')->toggleable(),
            Fields::statusColumn(),
            static::updatedColumn(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPages::route('/'),
            'create' => Pages\CreatePage::route('/create'),
            'edit' => Pages\EditPage::route('/{record}/edit'),
        ];
    }
}
