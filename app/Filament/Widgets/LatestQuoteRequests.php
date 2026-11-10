<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\QuoteRequestResource;
use App\Models\QuoteRequest;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class LatestQuoteRequests extends TableWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Latest quote requests';

    public static function canView(): bool
    {
        return auth()->user()?->hasAnyRole(['Admin', 'Sales']) ?? false;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(QuoteRequest::query()->latest()->limit(5))
            ->paginated(false)
            ->columns([
                Tables\Columns\TextColumn::make('created_at')->label('Received')->since(),
                Tables\Columns\TextColumn::make('name')->description(fn ($record) => $record->email),
                Tables\Columns\TextColumn::make('intent')->label('Interest')->badge()->color('gray'),
                Tables\Columns\TextColumn::make('dates'),
                Tables\Columns\TextColumn::make('status')->badge()
                    ->color(fn (string $state) => $state === 'new' ? 'warning' : 'gray'),
            ])
            ->recordUrl(fn ($record) => QuoteRequestResource::getUrl('edit', ['record' => $record]))
            ->emptyStateHeading('No quote requests yet');
    }
}
