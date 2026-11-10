<?php

namespace App\Filament\Resources\MenuResource\RelationManagers;

use App\Models\MenuItem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'allItems';

    protected static ?string $title = 'Items';

    /** Public site routes an item can point to. */
    public static function routeOptions(): array
    {
        return collect(Route::getRoutes()->getRoutesByName())
            ->keys()
            ->reject(fn ($name) => Str::startsWith($name, ['filament.', 'livewire.', 'storage.', 'sanctum.', 'ignition.']) || in_array($name, ['login', 'up'], true))
            ->sort()->mapWithKeys(fn ($name) => [$name => $name])->all();
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('label')->required(),
            Forms\Components\Select::make('parent_id')->label('Parent item')
                ->options(fn () => $this->getOwnerRecord()->allItems()->pluck('label', 'id'))->placeholder('Top level'),
            Forms\Components\Select::make('route')->label('Link to page')->options(self::routeOptions())->searchable()->live()
                ->helperText('Leave empty to use a custom URL.'),
            Forms\Components\TextInput::make('route_param')->label('Page slug')
                ->visible(fn (Get $get) => Str::endsWith((string) $get('route'), '.show'))->placeholder('events'),
            Forms\Components\TextInput::make('url')->label('Custom URL')->visible(fn (Get $get) => blank($get('route'))),
            Forms\Components\TextInput::make('active_routes')->label('Highlight on routes')
                ->placeholder('services.*,case-studies.*')->helperText('Comma separated route patterns (header menus only).'),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('label')
            ->columns([
                Tables\Columns\TextColumn::make('label')
                    ->description(fn (MenuItem $record) => $record->parent ? '↳ under '.$record->parent->label : null),
                Tables\Columns\TextColumn::make('link')->getStateUsing(fn (MenuItem $record) => rescue(fn () => $record->href(), '—', false))->color('gray'),
                Tables\Columns\TextColumn::make('active_routes')->label('Highlight')->toggleable(),
            ])
            ->reorderable('sort_order')->defaultSort('sort_order')
            ->headerActions([Tables\Actions\CreateAction::make()])
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()])
            ->bulkActions([Tables\Actions\DeleteBulkAction::make()]);
    }
}
