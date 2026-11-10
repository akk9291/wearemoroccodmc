<?php

namespace App\Filament\Support;

use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\ExportBulkAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

/** Website submissions: read-only data, editable status + internal notes, CSV export. */
abstract class LeadResource extends Resource
{
    protected static ?string $navigationGroup = 'Leads';

    /** @var class-string<\Filament\Actions\Exports\Exporter> */
    protected static string $exporter;

    public const STATUSES = ['new' => 'New', 'contacted' => 'Contacted', 'quoted' => 'Quoted', 'won' => 'Won', 'lost' => 'Lost'];

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::where('status', 'new')->count();

        return $count ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'New';
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes([SoftDeletingScope::class]);
    }

    /** Submitted values shown read-only, then the fields the team works with. */
    protected static function leadForm(Forms\Form $form, array $fields): Forms\Form
    {
        $readonly = [];
        foreach ($fields as $name => $label) {
            $readonly[] = in_array($name, ['note', 'mood', 'stays', 'prio'], true)
                ? Forms\Components\Textarea::make($name)->label($label)->disabled()->rows(3)->columnSpanFull()
                : Forms\Components\TextInput::make($name)->label($label)->disabled();
        }
        $readonly[] = Forms\Components\Placeholder::make('received')->label('Received')
            ->content(fn ($record) => $record?->created_at?->format('d M Y H:i'));

        return $form->schema([
            Forms\Components\Section::make('Submission')->description('As sent from the website (read-only).')->columns(2)->schema($readonly),
            Forms\Components\Section::make('Follow-up')->schema([
                Forms\Components\Select::make('status')->options(static::STATUSES)->required()->native(false),
                Forms\Components\Textarea::make('admin_notes')->label('Notes (internal)')->rows(4),
            ]),
        ])->columns(1);
    }

    protected static function leadTable(Table $table, array $columns): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')->label('Received')->dateTime('d M Y H:i')->sortable(),
                ...$columns,
                Tables\Columns\TextColumn::make('status')->badge()->sortable()
                    ->color(fn (string $state) => match ($state) {
                        'new' => 'warning', 'contacted' => 'info', 'quoted' => 'primary', 'won' => 'success', default => 'gray',
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options(static::STATUSES)->multiple(),
                static::dateFilter(),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->headerActions([ExportAction::make()->exporter(static::$exporter)->label('Export CSV')])
            ->actions([
                Tables\Actions\EditAction::make()->label('Open'),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    ExportBulkAction::make()->exporter(static::$exporter)->label('Export selected'),
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function dateFilter(): Tables\Filters\Filter
    {
        return Tables\Filters\Filter::make('created_at')->label('Received')
            ->form([
                Forms\Components\DatePicker::make('from'),
                Forms\Components\DatePicker::make('until'),
            ])
            ->query(fn (Builder $query, array $data) => $query
                ->when($data['from'] ?? null, fn ($q, $d) => $q->whereDate('created_at', '>=', $d))
                ->when($data['until'] ?? null, fn ($q, $d) => $q->whereDate('created_at', '<=', $d)))
            ->indicateUsing(fn (array $data) => array_filter([
                ($data['from'] ?? null) ? 'From '.$data['from'] : null,
                ($data['until'] ?? null) ? 'Until '.$data['until'] : null,
            ]));
    }
}
