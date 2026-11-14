<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RedirectResource\Pages;
use App\Models\Redirect;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Validation\Rules\Unique;

/** Redirects: old address -> new address (301/302), with a hit counter. Slug changes add rows automatically. */
class RedirectResource extends Resource
{
    protected static ?string $model = Redirect::class;

    protected static ?string $navigationGroup = 'Site';

    protected static ?string $navigationIcon = 'heroicon-o-arrow-uturn-right';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('from_path')->label('From (old address)')->required()->maxLength(255)
                ->placeholder('/old-page')->helperText('Path only, e.g. /old-page or /old-page.html.')
                ->dehydrateStateUsing(fn (?string $state) => Redirect::normalize($state))
                ->unique(ignoreRecord: true)
                ->rule(fn () => function (string $attribute, $value, \Closure $fail) {
                    if (in_array(Redirect::normalize($value), ['/', '/admin', '/forms'], true) || str_starts_with(Redirect::normalize($value), '/admin/')) {
                        $fail('This address cannot be redirected.');
                    }
                }),
            Forms\Components\TextInput::make('to_path')->label('To (new address)')->required()->maxLength(1024)
                ->placeholder('/new-page')->helperText('A path on this site (/journeys) or a full URL (https://...).')
                ->different('from_path'),
            Forms\Components\Select::make('status_code')->label('Type')->options(Redirect::CODES)->default(301)->required()->native(false),
            Forms\Components\Placeholder::make('stats')->label('Used')
                ->content(fn (?Redirect $record) => $record ? $record->hits.' times'.($record->last_hit_at ? ', last '.$record->last_hit_at->diffForHumans() : '') : 'Not yet'),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('from_path')->label('From')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('to_path')->label('To')->searchable()->limit(60),
                Tables\Columns\TextColumn::make('status_code')->label('Type')->badge()->color(fn (int $state) => $state === 301 ? 'success' : 'warning'),
                Tables\Columns\TextColumn::make('hits')->numeric()->sortable(),
                Tables\Columns\TextColumn::make('last_hit_at')->label('Last used')->since()->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('source')->badge()->color('gray')
                    ->formatStateUsing(fn (string $state) => Redirect::SOURCES[$state] ?? $state)->toggleable(),
            ])
            ->defaultSort('updated_at', 'desc')
            ->filters([Tables\Filters\SelectFilter::make('source')->options(Redirect::SOURCES)])
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()])
            ->bulkActions([Tables\Actions\DeleteBulkAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRedirects::route('/'),
            'create' => Pages\CreateRedirect::route('/create'),
            'edit' => Pages\EditRedirect::route('/{record}/edit'),
        ];
    }
}
