<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TestimonialResource\Pages;
use App\Filament\Support\ContentResource;
use App\Filament\Support\Fields;
use App\Models\Testimonial;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;

class TestimonialResource extends ContentResource
{
    protected static ?string $model = Testimonial::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?int $navigationSort = 10;

    public const PLACEMENTS = ['guests' => 'Guest letters (home, why us)', 'services' => 'Service pages'];

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Textarea::make('quote')->required()->rows(4)->columnSpanFull(),
            Forms\Components\TextInput::make('name')->required(),
            Forms\Components\TextInput::make('role'),
            Forms\Components\TextInput::make('company'),
            Forms\Components\TextInput::make('trip')->placeholder('Imperial & Sahara · 10 nights'),
            Forms\Components\Select::make('placement')->options(self::PLACEMENTS)->default('guests')->required(),
            Fields::status(),
            Fields::image('image', 'Photo'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return static::contentTable($table, [
            Fields::imageColumn('image', ''),
            Tables\Columns\TextColumn::make('name')->searchable()->description(fn ($record) => $record->role),
            Tables\Columns\TextColumn::make('quote')->limit(70)->wrap(),
            Tables\Columns\TextColumn::make('placement')->badge()->color('gray'),
            Fields::statusColumn(),
        ], [
            Tables\Filters\SelectFilter::make('placement')->options(self::PLACEMENTS),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTestimonials::route('/'),
            'create' => Pages\CreateTestimonial::route('/create'),
            'edit' => Pages\EditTestimonial::route('/{record}/edit'),
        ];
    }
}
