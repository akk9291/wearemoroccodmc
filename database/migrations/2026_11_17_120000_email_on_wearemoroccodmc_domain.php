<?php

use App\Support\ContentCache;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * The site moved to wearemoroccodmc.com: info@wearemoroccotravel.com becomes info@wearemoroccodmc.com in every
 * content table (page copy, sections, footer/menu, settings, mailto links) and as the admin login email.
 * Leads and other visitor data are not touched.
 */
return new class extends Migration
{
    public const OLD = 'info@wearemoroccotravel.com';

    public const NEW = 'info@wearemoroccodmc.com';

    private const TABLES = ['settings', 'pages', 'services', 'destinations', 'experiences', 'itineraries', 'itinerary_days',
        'case_studies', 'journal_posts', 'journal_categories', 'faqs', 'menus', 'menu_items', 'testimonials', 'partners', 'redirects'];

    public function up(): void
    {
        foreach (self::TABLES as $table) {
            if (! Schema::hasTable($table)) {
                continue;
            }
            foreach (Schema::getColumns($table) as $column) {
                if (! preg_match('/char|text|json/i', $column['type_name'])) {
                    continue;
                }
                $name = $column['name'];
                DB::table($table)->where($name, 'like', '%'.self::OLD.'%')
                    ->update([$name => DB::raw('REPLACE('.DB::getQueryGrammar()->wrap($name).", '".self::OLD."', '".self::NEW."')")]);
            }
        }

        if (! DB::table('users')->where('email', self::NEW)->exists()) {
            DB::table('users')->where('email', self::OLD)->update(['email' => self::NEW]);
        }

        ContentCache::flush();
    }

    public function down(): void
    {
    }
};
