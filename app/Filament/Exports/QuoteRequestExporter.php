<?php

namespace App\Filament\Exports;

use App\Models\QuoteRequest;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class QuoteRequestExporter extends Exporter
{
    protected static ?string $model = QuoteRequest::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('name'),
            ExportColumn::make('email'),
            ExportColumn::make('intent'),
            ExportColumn::make('party'),
            ExportColumn::make('travelers'),
            ExportColumn::make('dates'),
            ExportColumn::make('length'),
            ExportColumn::make('pace'),
            ExportColumn::make('mood'),
            ExportColumn::make('stays'),
            ExportColumn::make('prio'),
            ExportColumn::make('lead'),
            ExportColumn::make('note'),
            ExportColumn::make('status'),
            ExportColumn::make('admin_notes'),
            ExportColumn::make('source_page'),
            ExportColumn::make('ip'),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
            ExportColumn::make('deleted_at'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your quote request export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
