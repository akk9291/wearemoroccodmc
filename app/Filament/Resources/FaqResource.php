<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FaqResource\Pages;
use App\Filament\Support\ContentResource;
use App\Filament\Support\Fields;
use App\Models\Faq;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;

class FaqResource extends ContentResource
{
    protected static ?string $model = Faq::class;

    protected static ?string $navigationIcon = 'heroicon-o-question-mark-circle';

    protected static ?int $navigationSort = 9;

    protected static ?string $navigationLabel = 'FAQs';

    protected static ?string $modelLabel = 'FAQ';

    /** Where each group is shown on the site. */
    public const GROUPS = [
        'home' => 'Home page',
        'planning' => 'FAQ page · Planning & seasons',
        'travel' => 'FAQ page · Travel & arrival',
        'safety' => 'FAQ page · Safety & practicalities',
        'how' => 'FAQ page · How we work',
        'service-tailor-made' => 'Service · Tailor-made',
        'service-incentives' => 'Service · Incentives',
        'service-events' => 'Service · Events',
        'service-ground-handling' => 'Service · Ground handling',
    ];

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('group')->options(self::GROUPS)->required()->native(false),
            Fields::status(),
            Forms\Components\Textarea::make('question')->required()->rows(2)->columnSpanFull(),
            Fields::code('answer', 'Answer (HTML, e.g. <p>…</p>)', 6)->required()->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return static::contentTable($table, [
            Tables\Columns\TextColumn::make('question')->searchable()->limit(80)->wrap(),
            Tables\Columns\TextColumn::make('group')->badge()->color('gray')
                ->formatStateUsing(fn (string $state) => self::GROUPS[$state] ?? $state),
            Fields::statusColumn(),
        ], [
            Tables\Filters\SelectFilter::make('group')->options(self::GROUPS),
        ])->defaultGroup('group');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFaqs::route('/'),
            'create' => Pages\CreateFaq::route('/create'),
            'edit' => Pages\EditFaq::route('/{record}/edit'),
        ];
    }
}
