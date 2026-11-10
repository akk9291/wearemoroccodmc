<?php

namespace App\Filament\Resources;

use App\Filament\Exports\NewsletterSubscriberExporter;
use App\Filament\Resources\NewsletterSubscriberResource\Pages;
use App\Filament\Support\LeadResource;
use App\Models\NewsletterSubscriber;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\ExportBulkAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class NewsletterSubscriberResource extends LeadResource
{
    protected static ?string $model = NewsletterSubscriber::class;

    protected static string $exporter = NewsletterSubscriberExporter::class;

    protected static ?string $navigationIcon = 'heroicon-o-at-symbol';

    protected static ?int $navigationSort = 3;

    /** Subscribers have no status: the badge counts sign-ups of the last 7 days. */
    public static function getNavigationBadge(): ?string
    {
        $count = NewsletterSubscriber::where('created_at', '>=', now()->subDays(7))->count();

        return $count ? (string) $count : null;
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Last 7 days';
    }

    public static function getEloquentQuery(): Builder
    {
        return NewsletterSubscriber::query();
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('email')->email()->disabled(),
            Forms\Components\TextInput::make('source')->disabled(),
            Forms\Components\DateTimePicker::make('confirmed_at'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')->label('Subscribed')->dateTime('d M Y H:i')->sortable(),
                Tables\Columns\TextColumn::make('email')->searchable()->copyable(),
                Tables\Columns\TextColumn::make('source')->badge()->color('gray'),
                Tables\Columns\IconColumn::make('confirmed_at')->label('Confirmed')->boolean()
                    ->getStateUsing(fn ($record) => $record->confirmed_at !== null),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\TernaryFilter::make('confirmed_at')->label('Confirmed')->nullable(),
                static::dateFilter(),
            ])
            ->headerActions([ExportAction::make()->exporter(static::$exporter)->label('Export CSV')])
            ->actions([Tables\Actions\EditAction::make()->label('Open'), Tables\Actions\DeleteAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([
                ExportBulkAction::make()->exporter(static::$exporter)->label('Export selected'),
                Tables\Actions\DeleteBulkAction::make(),
            ])]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNewsletterSubscribers::route('/'),
            'edit' => Pages\EditNewsletterSubscriber::route('/{record}/edit'),
        ];
    }
}
