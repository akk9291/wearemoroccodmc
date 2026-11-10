<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JournalPostResource\Pages;
use App\Filament\Support\ContentResource;
use App\Filament\Support\Fields;
use App\Models\JournalPost;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;

class JournalPostResource extends ContentResource
{
    protected static ?string $model = JournalPost::class;

    protected static ?string $navigationIcon = 'heroicon-o-newspaper';

    protected static ?int $navigationSort = 7;

    protected static ?string $navigationLabel = 'Journal posts';

    protected static ?string $recordTitleAttribute = 'title';

    public static function publicUrl($record): ?string
    {
        return route('journal.show', $record);
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Post')->columns(2)->schema([
                ...Fields::titleAndSlug(),
                Forms\Components\Select::make('category_id')->relationship('category', 'name')->preload()
                    ->createOptionForm([
                        Forms\Components\TextInput::make('name')->required(),
                        Forms\Components\TextInput::make('slug')->required()->alphaDash(),
                    ]),
                Forms\Components\TextInput::make('author'),
                Forms\Components\TextInput::make('reading_minutes')->numeric()->suffix('min'),
                Forms\Components\DateTimePicker::make('published_at'),
                Forms\Components\TextInput::make('published_label')->helperText('Shown instead of the date, e.g. while it is a placeholder.'),
                Forms\Components\Toggle::make('is_featured')->label('Featured on /journal')->inline(false),
                Fields::status(),
                Forms\Components\Textarea::make('excerpt')->rows(2)->columnSpanFull(),
                Fields::image('cover_image', 'Cover image'),
                Forms\Components\TextInput::make('cover_image_alt')->label('Cover image alt text'),
            ]),
            Fields::hero(),
            Forms\Components\Section::make('Article')->schema([
                Fields::code('body', 'Body (HTML, optional)', 8),
                Fields::sections(),
            ]),
            Fields::seo(),
        ])->columns(1);
    }

    public static function table(Table $table): Table
    {
        return static::contentTable($table, [
            Fields::imageColumn('cover_image', ''),
            Tables\Columns\TextColumn::make('title')->searchable()->sortable()->limit(60),
            Tables\Columns\TextColumn::make('category.name')->badge()->color('gray'),
            Tables\Columns\IconColumn::make('is_featured')->label('Featured')->boolean(),
            Fields::statusColumn(),
            Tables\Columns\TextColumn::make('published_at')->dateTime('d M Y')->sortable()->toggleable(),
        ], [
            Tables\Filters\SelectFilter::make('category')->relationship('category', 'name'),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListJournalPosts::route('/'),
            'create' => Pages\CreateJournalPost::route('/create'),
            'edit' => Pages\EditJournalPost::route('/{record}/edit'),
        ];
    }
}
