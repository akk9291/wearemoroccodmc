<?php

use App\Models\Page;
use App\Support\ContentCache;
use Illuminate\Database\Migrations\Migration;

/**
 * About page team section: the two "[Name]" placeholder cards get the HTML `hidden` attribute (kept in the
 * section HTML, so removing `hidden` in the admin brings them back), the "[Personal line]" placeholders are
 * removed from the co-founder cards, and the grid becomes a two-card layout.
 */
return new class extends Migration
{
    public function up(): void
    {
        $about = Page::withTrashed()->where('slug', 'about')->first();
        if (! $about) {
            return;
        }
        $sections = $about->sections ?? [];
        $changed = false;
        foreach ($sections as $i => $block) {
            $html = $block['html'] ?? '';
            if (! str_contains($html, 'class="eyebrow">The team<')) {
                continue;
            }
            $new = static::transform($html);
            if ($new !== $html) {
                $sections[$i]['html'] = $new;
                $changed = true;
            }
        }
        if ($changed) {
            $about->sections = $sections;
            $about->save();
            ContentCache::flush();
        }
    }

    public static function transform(string $html): string
    {
        // hide every member card whose name is still the [Name] placeholder
        $html = preg_replace_callback('#<article class="member reveal"( style="[^"]*")?>(.*?)</article>#s', function ($m) {
            return str_contains($m[2], '<h3>[Name]</h3>') ? '<article class="member reveal" hidden'.$m[1].'>'.$m[2].'</article>' : $m[0];
        }, $html);
        // drop the "[Personal line]" placeholder quotes
        $html = str_replace('<p>“[Personal line]”</p>', '', $html);
        // two visible cards: a two-column grid kept to a sensible width
        $visible = preg_match_all('#<article class="member reveal"(?! hidden)#', $html);
        if ($visible === 2) {
            $html = str_replace('<div class="grid g-4 mt-14">', '<div class="grid g-2 mt-14 team-duo">', $html);
        }

        return $html;
    }

    public function down(): void
    {
    }
};
