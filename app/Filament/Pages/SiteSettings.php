<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

/** One page for the site-wide settings rows (contact details, socials, footer text). */
class SiteSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationGroup = 'Site';

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationLabel = 'Settings';

    protected static ?string $title = 'Site settings';

    protected static ?int $navigationSort = 2;

    protected static string $view = 'filament.pages.site-settings';

    /** key => group */
    protected const KEYS = [
        'phone' => 'contact', 'phone_display' => 'contact', 'email' => 'contact', 'address' => 'contact',
        'socials' => 'social',
        'footer_blurb' => 'footer', 'copyright' => 'footer', 'footer_tagline' => 'footer', 'footer_columns' => 'footer',
    ];

    public ?array $data = [];

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('Admin') ?? false;
    }

    public function mount(): void
    {
        $values = Setting::allValues();
        $data = array_intersect_key($values, self::KEYS);
        $data['footer_columns'] = array_values($values['footer_columns'] ?? ['', '', '']) + ['', '', ''];
        $this->form->fill($data);
    }

    public function form(Form $form): Form
    {
        return $form->statePath('data')->schema([
            Forms\Components\Section::make('Contact')->columns(2)->schema([
                Forms\Components\TextInput::make('phone')->label('Phone (for tel: links)')->required()->placeholder('+212653051512'),
                Forms\Components\TextInput::make('phone_display')->label('Phone as shown')->required(),
                Forms\Components\TextInput::make('email')->email()->required(),
                Forms\Components\Textarea::make('address')->rows(2)->required(),
            ]),
            Forms\Components\Section::make('Social links')->schema([
                Forms\Components\Repeater::make('socials')->hiddenLabel()->schema([
                    Forms\Components\TextInput::make('label')->required(),
                    Forms\Components\TextInput::make('url')->required(),
                ])->columns(2)->reorderable()->defaultItems(0),
            ]),
            Forms\Components\Section::make('Footer')->columns(3)->schema([
                Forms\Components\Textarea::make('footer_blurb')->label('Intro text')->rows(3)->columnSpanFull(),
                Forms\Components\TextInput::make('footer_columns.0')->label('Column 1 heading'),
                Forms\Components\TextInput::make('footer_columns.1')->label('Column 2 heading'),
                Forms\Components\TextInput::make('footer_columns.2')->label('Column 3 heading'),
                Forms\Components\TextInput::make('copyright')->columnSpan(2),
                Forms\Components\TextInput::make('footer_tagline')->label('Tagline'),
            ]),
        ]);
    }

    protected function getFormActions(): array
    {
        return [Action::make('save')->label('Save settings')->submit('save')];
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $data['footer_columns'] = array_values($data['footer_columns'] ?? []);
        foreach (self::KEYS as $key => $group) {
            Setting::updateOrCreate(['key' => $key], ['value' => $data[$key] ?? null, 'group' => $group]);
        }
        Notification::make()->title('Settings saved')->success()->send();
    }
}
