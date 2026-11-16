<?php

namespace App\Filament\Support;

use App\Support\Content;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

/** Form fields and table columns shared by the content resources. */
class Fields
{
    public const STATUSES = ['published' => 'Published', 'draft' => 'Draft'];

    /** Title input that fills the slug while creating; slug stays editable. */
    public static function titleAndSlug(string $title = 'title'): array
    {
        return [
            TextInput::make($title)->required()->maxLength(255)
                ->live(onBlur: true)
                ->afterStateUpdated(function (?string $state, Set $set, Get $get, string $operation) {
                    if ($operation === 'create' && blank($get('slug'))) {
                        $set('slug', Str::slug(html_entity_decode(strip_tags((string) $state))));
                    }
                }),
            TextInput::make('slug')->required()->maxLength(255)->alphaDash()->unique(ignoreRecord: true)
                ->helperText('Part of the URL. Changing it breaks old links.'),
        ];
    }

    public static function status(): Select
    {
        return Select::make('status')->options(self::STATUSES)->default('published')->required()->native(false);
    }

    /**
     * Image stored as a public-disk path or a full URL (existing Pexels images).
     * Uploading a file stores it right away and puts its path in the text field.
     */
    public static function image(string $name, string $label = 'Image'): Group
    {
        return Group::make([
            TextInput::make($name)->label($label)->maxLength(1024)->live(onBlur: true)
                ->helperText('Image URL, or upload a file below.'),
            FileUpload::make($name.'__upload')->label('Upload '.Str::lower($label))
                ->image()->disk('public')->directory('uploads')->maxSize(8192)
                ->dehydrated(false)->live()
                // stored once, under a name derived from the temp file, so repeated updates don't duplicate it
                ->afterStateUpdated(function ($state, Set $set) use ($name) {
                    $file = is_array($state) ? collect($state)->first() : $state;
                    if ($file instanceof TemporaryUploadedFile) {
                        $target = 'uploads/'.md5($file->getFilename()).'.'.($file->guessExtension() ?: 'jpg');
                        if (! \Illuminate\Support\Facades\Storage::disk('public')->exists($target)) {
                            $file->storeAs('uploads', basename($target), 'public');
                        }
                        \App\Support\ImageVariants::generate($target); // WebP + 800/1600 px variants for srcset
                        $set($name, $target);
                    }
                })
                ->saveUploadedFileUsing(fn () => null),   // already stored above; the path lives in the text field
            Placeholder::make($name.'__preview')->hiddenLabel()
                ->content(fn (Get $get) => filled($get($name))
                    ? new HtmlString('<img src="'.e(Content::img($get($name))).'" style="max-height:140px;border-radius:6px">')
                    : null)
                ->visible(fn (Get $get) => filled($get($name))),
        ]);
    }

    public static function imageColumn(string $name, string $label = 'Image'): ImageColumn
    {
        return ImageColumn::make($name)->label($label)
            ->getStateUsing(fn ($record) => $record->{$name} ? Content::img($record->{$name}) : null)
            ->height(44)->extraImgAttributes(['style' => 'object-fit:cover;border-radius:4px']);
    }

    public static function statusColumn(): TextColumn
    {
        return TextColumn::make('status')->badge()->sortable()
            ->color(fn (?string $state) => $state === 'published' ? 'success' : 'gray');
    }

    public static function statusFilter(): SelectFilter
    {
        return SelectFilter::make('status')->options(self::STATUSES);
    }

    public static function seo(): Section
    {
        return Section::make('SEO')->collapsible()->collapsed()->schema([
            TextInput::make('meta_title')->maxLength(255)->helperText('Browser title and og:title.'),
            Textarea::make('meta_description')->rows(3),
            self::image('og_image', 'Social share image (og:image)'),
        ]);
    }

    /** The phero hero at the top of a page. */
    public static function hero(bool $withExtra = true): Section
    {
        return Section::make('Hero')->collapsible()->schema(array_filter([
            Select::make('hero_variant')->options(['short' => 'Short'])->placeholder('Normal')->native(false),
            self::image('hero_image', 'Hero image'),
            TextInput::make('hero_image_alt')->label('Hero image alt text')->maxLength(255),
            Repeater::make('hero_crumbs')->label('Breadcrumbs')->schema([
                TextInput::make('label')->required(),
                TextInput::make('url')->placeholder('/services (empty = current page)'),
            ])->columns(2)->reorderable()->defaultItems(0)->collapsible(),
            TextInput::make('hero_eyebrow')->label('Eyebrow')->helperText('Small line above the heading. HTML allowed.'),
            Textarea::make('hero_title')->label('Heading (H1)')->rows(2)->helperText('HTML allowed, e.g. <span class="it block">…</span>'),
            Textarea::make('hero_subtitle')->label('Lead paragraph')->rows(3),
            $withExtra ? self::code('hero_extra', 'Extra HTML (buttons, facts)', 4) : null,
        ]))->columns(1);
    }

    /** Raw HTML textarea in a monospace font. */
    public static function code(string $name, string $label, int $rows = 14): Textarea
    {
        return Textarea::make($name)->label($label)->rows($rows)
            ->extraInputAttributes(['style' => 'font-family:ui-monospace,Consolas,monospace;font-size:12px;line-height:1.5'])
            ->helperText('HTML. Links to site pages can be written as /path. Lists come from markers like <!--wam:faqs group="home"-->.');
    }

    /**
     * The "sections" JSON column: ordered blocks. Stored flat as {type, ...fields}; the Builder
     * works with {type, data}. The edit/create pages convert both ways (see ConvertsSections).
     */
    public static function sections(string $name = 'sections'): Builder
    {
        return Builder::make($name)->label('Page sections')
            ->blocks([
                Block::make('html')->label(fn (?array $state) => $state === null ? 'HTML section' : self::blockLabel(['type' => 'html', 'data' => $state]))->icon('heroicon-o-code-bracket')
                    ->schema([self::code('html', 'HTML', 18), self::hiddenToggle()]),
                Block::make('cta')->label(fn (?array $state) => $state === null ? 'Call to action (closing banner)' : self::blockLabel(['type' => 'cta', 'data' => $state]))->icon('heroicon-o-megaphone')
                    ->schema([...self::ctaFields(), self::hiddenToggle()]),
                Block::make('script')->label(fn (?array $state) => $state === null ? 'Data script' : self::blockLabel(['type' => 'script', 'data' => $state]))->icon('heroicon-o-bolt')
                    ->schema([
                        Select::make('name')->options(['journeys' => 'Signature journeys (WAM_JOURNEYS)', 'stories' => 'Guest letters (WAM_STORIES)'])->required()->live(),
                        Select::make('placement')->options(['guests' => 'Guests', 'services' => 'Services (sample)'])
                            ->visible(fn (Get $get) => $get('name') === 'stories'),
                        self::hiddenToggle(),
                    ]),
            ])
            ->blockPreviews(false)->collapsible()->collapsed()->reorderable()->cloneable();
    }

    /** Keeps a block in the page but leaves it off the website (e.g. content waiting for approval). */
    public static function hiddenToggle(): \Filament\Forms\Components\Toggle
    {
        return \Filament\Forms\Components\Toggle::make('hidden')->label('Hide this section on the website')
            ->helperText('The section is kept here and can be shown again by turning this off.')->default(false);
    }

    public static function ctaFields(): array
    {
        return [
            self::image('image', 'Background image'),
            TextInput::make('image_alt')->label('Image alt text'),
            TextInput::make('label')->label('Small label')->required(),
            Textarea::make('title')->label('Heading')->rows(2)->required()->helperText('HTML allowed.'),
            Textarea::make('lead')->rows(2),
            self::code('ctas', 'Buttons (HTML)', 4),
        ];
    }

    protected static function blockLabel(array $state): string
    {
        $data = $state['data'] ?? $state;
        $type = $state['type'] ?? null;
        if ($type === 'cta') {
            return 'Call to action: '.strip_tags((string) ($data['label'] ?? ''));
        }
        if ($type === 'script') {
            return 'Data script: '.($data['name'] ?? '');
        }
        $html = (string) ($data['html'] ?? '');
        $prefix = ! empty($data['hidden']) ? '[Hidden] HTML: ' : 'HTML: ';
        if (preg_match('/class="(?:eyebrow|label|kicker)">([^<]+)/', $html, $m)) {
            return $prefix.html_entity_decode($m[1]);
        }

        return $prefix.Str::limit(trim(strip_tags($html)), 60);
    }

    /** Repeater of plain strings, stored as a JSON list. */
    public static function lines(string $name, string $label, string $placeholder = ''): Repeater
    {
        return Repeater::make($name)->label($label)
            ->simple(TextInput::make('value')->placeholder($placeholder)->required())
            ->reorderable()->defaultItems(0);
    }

    /** Gallery: list of {image, alt}. */
    public static function gallery(string $name = 'gallery'): Repeater
    {
        return Repeater::make($name)->schema([
            self::image('image'),
            TextInput::make('alt')->label('Alt text'),
        ])->reorderable()->collapsible()->defaultItems(0)->grid(2)
            ->itemLabel(fn (array $state) => $state['alt'] ?? null);
    }
}

