<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JournalCategoryResource\Pages;
use App\Filament\Support\Fields;
use App\Models\JournalCategory;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class JournalCategoryResource extends Resource
{
    protected static ?string $model = JournalCategory::class;

    protected static ?string $navigationGroup = 'Content';

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?int $navigationSort = 8;

    protected static ?string $navigationLabel = 'Journal categories';

    public static function form(Form $form): Form
    {
        return $form->schema(Fields::titleAndSlug('name'));
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('slug')->color('gray'),
                Tables\Columns\TextColumn::make('posts_count')->counts('posts')->label('Posts'),
            ])
            ->reorderable('sort_order')->defaultSort('sort_order')
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()])
            ->bulkActions([Tables\Actions\DeleteBulkAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListJournalCategories::route('/'),
            'create' => Pages\CreateJournalCategory::route('/create'),
            'edit' => Pages\EditJournalCategory::route('/{record}/edit'),
        ];
    }
}
