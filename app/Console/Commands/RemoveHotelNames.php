<?php

namespace App\Console\Commands;

use App\Support\ContentCache;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Replaces hotel / riad / camp brand names in site content with the generic "<City> properties" form.
 * Updates the existing rows in place (keeps admin edits) and, with --seed-files, the seed JSON too.
 * Safe to run more than once. Leads (quote requests, contact messages) are never touched.
 */
class RemoveHotelNames extends Command
{
    protected $signature = 'content:remove-hotel-names {--dry-run : Report what would change without saving} {--seed-files : Also rewrite database/seeders/data/*.json}';

    protected $description = 'Replace hotel and property brand names in site content with "<City> properties"';

    /** Content tables only. */
    private const TABLES = ['pages', 'services', 'destinations', 'itineraries', 'itinerary_days', 'experiences',
        'case_studies', 'journal_posts', 'journal_categories', 'faqs', 'testimonials', 'settings', 'menus', 'menu_items'];

    /** Ordered: full phrases first, then single names as a fallback for anything an admin added. */
    public const REPLACEMENTS = [
        // Home + Marrakech destination page: "Handpicked stays" cards
        '<h3>Royal Mansour Marrakech</h3><p>A city of private riads inside the walls — commissioned for heads of state, now yours for a week.</p>'
            => '<h3>Marrakech properties</h3><p>A city of private riads inside the walls — fit for heads of state, now yours for a week.</p>',
        '<h3>La Mamounia</h3><p>The legendary palace garden. Churchill painted here. You will understand why by the second breakfast.</p>'
            => '<h3>Marrakech garden properties</h3><p>Legendary palace gardens and quiet courtyards. You will understand why by the second breakfast.</p>',
        '<h3>Dar Ahlam</h3>' => '<h3>Skoura properties</h3>',
        // Home: "Stays with a sense of place"
        'Royal Mansour, La Mamounia, Dar Ahlam, and the Sahara camps we actually sleep-test.'
            => 'Hand-picked Marrakech properties, Skoura properties, and the Sahara camps we actually sleep-test.',
        // About: "Relationships, not affiliations"
        'Royal Mansour, Oberoi, La Mamounia, Dar Ahlam, and the camps we will not name in a brochure.'
            => 'Marrakech properties, Skoura properties, and the Merzouga camps we will not name in a brochure.',
        // FAQ: "How far in advance should we plan?"
        'For Royal Mansour, La Mamounia, Dar Ahlam, and the better Sahara camps,'
            => 'For the most sought-after Marrakech properties, Skoura properties and Merzouga properties,',
        // Journey builder: "How should your rooms feel?"
        'Royal Mansour · La Mamounia' => 'Marrakech properties · grand palaces',
        'Oberoi · Mandarin Oriental' => 'Marrakech properties · resort estates',
        'Dar Ahlam · intimate kasbahs' => 'Skoura properties · intimate kasbahs',
        // Fallbacks
        'Royal Mansour Marrakech' => 'Marrakech properties',
        'Royal Mansour' => 'Marrakech properties',
        'La Mamounia' => 'Marrakech properties',
        'Mamounia' => 'Marrakech properties',
        'Mandarin Oriental' => 'Marrakech properties',
        'The Oberoi' => 'Marrakech properties',
        'Oberoi' => 'Marrakech properties',
        'Amanjena' => 'Marrakech properties',
        'Kasbah Tamadot' => 'High Atlas properties',
        'Dar Ahlam' => 'Skoura properties',
    ];

    public function handle(): int
    {
        $dry = (bool) $this->option('dry-run');
        $rows = 0;

        foreach (self::TABLES as $table) {
            if (! Schema::hasTable($table)) {
                continue;
            }
            $columns = collect(Schema::getColumns($table))
                ->filter(fn ($c) => preg_match('/char|text|json/i', $c['type_name'] ?? $c['type']))
                ->pluck('name')->all();
            if (! $columns || ! Schema::hasColumn($table, 'id')) {
                continue;
            }
            DB::table($table)->orderBy('id')->each(function ($row) use ($table, $columns, $dry, &$rows) {
                $changes = [];
                foreach ($columns as $col) {
                    $value = $row->$col ?? null;
                    if (! is_string($value) || $value === '') {
                        continue;
                    }
                    $new = $this->replaceInValue($value);
                    if ($new !== $value) {
                        $changes[$col] = $new;
                        $this->line("  {$table}#{$row->id}.{$col}");
                    }
                }
                if ($changes) {
                    $rows++;
                    if (! $dry) {
                        DB::table($table)->where('id', $row->id)->update($changes);
                    }
                }
            });
        }

        if ($this->option('seed-files')) {
            foreach (glob(database_path('seeders/data/*.json')) as $file) {
                $text = file_get_contents($file);
                $new = strtr($text, self::REPLACEMENTS);
                if ($new !== $text) {
                    $this->line('  seed file: '.basename($file));
                    if (! $dry) {
                        file_put_contents($file, $new);
                    }
                }
            }
        }

        if (! $dry && $rows) {
            ContentCache::flush();
        }
        $this->info(($dry ? '[dry run] ' : '')."Rows updated: {$rows}");

        return self::SUCCESS;
    }

    /** JSON columns are decoded so names stored with \u escapes are found too; other text is replaced directly. */
    private function replaceInValue(string $value): string
    {
        $first = ltrim($value)[0] ?? '';
        if ($first === '[' || $first === '{') {
            $decoded = json_decode($value, true);
            if (is_array($decoded)) {
                $walked = $this->walk($decoded);

                return $walked === $decoded ? $value : json_encode($walked, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            }
        }

        return strtr($value, self::REPLACEMENTS);
    }

    private function walk(mixed $data): mixed
    {
        if (is_array($data)) {
            return array_map(fn ($v) => $this->walk($v), $data);
        }

        return is_string($data) ? strtr($data, self::REPLACEMENTS) : $data;
    }
}
