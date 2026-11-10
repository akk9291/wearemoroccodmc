<?php

namespace App\Filament\Support;

use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletingScope;

/** Shared behaviour of the "Content" resources: soft deletes, status, drag-and-drop ordering. */
abstract class ContentResource extends Resource
{
    protected static ?string $navigationGroup = 'Content';

    /** Public URL of a record, for the "View on site" action. */
    public static function publicUrl($record): ?string
    {
        return null;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes([SoftDeletingScope::class]);
    }

    protected static function contentTable(Table $table, array $columns, array $filters = [], bool $reorder = true): Table
    {
        $table = $table
            ->columns($columns)
            ->filters([...$filters, Fields::statusFilter(), Tables\Filters\TrashedFilter::make()])
            ->actions([
                Tables\Actions\Action::make('view_site')->label('View')->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn ($record) => static::publicUrl($record), shouldOpenInNewTab: true)
                    ->visible(fn ($record) => static::publicUrl($record) !== null && $record->status === 'published' && ! $record->trashed()),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('publish')->icon('heroicon-o-eye')
                        ->action(fn (Collection $records) => $records->each->update(['status' => 'published']))
                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\BulkAction::make('draft')->label('Set to draft')->icon('heroicon-o-eye-slash')
                        ->action(fn (Collection $records) => $records->each->update(['status' => 'draft']))
                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                ]),
            ]);

        return $reorder ? $table->reorderable('sort_order')->defaultSort('sort_order') : $table;
    }

    protected static function updatedColumn(): Tables\Columns\TextColumn
    {
        return Tables\Columns\TextColumn::make('updated_at')->dateTime('d M Y H:i')->sortable()->toggleable(isToggledHiddenByDefault: true);
    }
}
