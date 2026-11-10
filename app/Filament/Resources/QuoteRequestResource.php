<?php

namespace App\Filament\Resources;

use App\Filament\Exports\QuoteRequestExporter;
use App\Filament\Resources\QuoteRequestResource\Pages;
use App\Filament\Support\LeadResource;
use App\Models\QuoteRequest;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;

class QuoteRequestResource extends LeadResource
{
    protected static ?string $model = QuoteRequest::class;

    protected static string $exporter = QuoteRequestExporter::class;

    protected static ?string $navigationIcon = 'heroicon-o-inbox-arrow-down';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return static::leadForm($form, [
            'name' => 'Name', 'email' => 'Email', 'intent' => 'Interest', 'party' => 'Travelers (party)',
            'travelers' => 'Travelers', 'dates' => 'Dates', 'length' => 'Length', 'pace' => 'Pace', 'lead' => 'Lead destination',
            'source_page' => 'Sent from page', 'ip' => 'IP address', 'mood' => 'Mood', 'stays' => 'Stays', 'prio' => 'Priorities',
            'note' => 'Message',
        ]);
    }

    public static function table(Table $table): Table
    {
        return static::leadTable($table, [
            Tables\Columns\TextColumn::make('name')->searchable()->description(fn ($record) => $record->email),
            Tables\Columns\TextColumn::make('email')->searchable()->toggleable(isToggledHiddenByDefault: true),
            Tables\Columns\TextColumn::make('intent')->label('Interest')->badge()->color('gray'),
            Tables\Columns\TextColumn::make('dates')->toggleable(),
            Tables\Columns\TextColumn::make('source_page')->label('From')->toggleable(isToggledHiddenByDefault: true),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListQuoteRequests::route('/'),
            'edit' => Pages\EditQuoteRequest::route('/{record}/edit'),
        ];
    }
}
