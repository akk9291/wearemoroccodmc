<?php

namespace App\Filament\Resources;

use App\Filament\Exports\ContactMessageExporter;
use App\Filament\Resources\ContactMessageResource\Pages;
use App\Filament\Support\LeadResource;
use App\Models\ContactMessage;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;

class ContactMessageResource extends LeadResource
{
    protected static ?string $model = ContactMessage::class;

    protected static string $exporter = ContactMessageExporter::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return static::leadForm($form, [
            'name' => 'Name', 'email' => 'Email', 'intent' => 'Interest', 'travelers' => 'Travelers', 'dates' => 'Dates',
            'note' => 'Message',
        ]);
    }

    public static function table(Table $table): Table
    {
        return static::leadTable($table, [
            Tables\Columns\TextColumn::make('name')->searchable()->description(fn ($record) => $record->email),
            Tables\Columns\TextColumn::make('email')->searchable()->toggleable(isToggledHiddenByDefault: true),
            Tables\Columns\TextColumn::make('intent')->label('Interest')->badge()->color('gray'),
            Tables\Columns\TextColumn::make('note')->label('Message')->limit(60)->toggleable(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContactMessages::route('/'),
            'edit' => Pages\EditContactMessage::route('/{record}/edit'),
        ];
    }
}
