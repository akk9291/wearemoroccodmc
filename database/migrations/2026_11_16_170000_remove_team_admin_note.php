<?php

use App\Models\Page;
use App\Support\ContentCache;
use Illuminate\Database\Migrations\Migration;

/** About page team intro: drop the internal note "Photos and names are added in the admin." shown to visitors. */
return new class extends Migration
{
    public const NOTE = ' Photos and names are added in the admin.';

    public function up(): void
    {
        $about = Page::withTrashed()->where('slug', 'about')->first();
        if (! $about || ! str_contains(json_encode($about->sections, JSON_UNESCAPED_UNICODE), trim(self::NOTE))) {
            return;
        }
        $about->sections = collect($about->sections)
            ->map(fn ($b) => isset($b['html']) ? ['html' => str_replace(self::NOTE, '', $b['html'])] + $b : $b)
            ->map(fn ($b) => ['type' => $b['type']] + $b)
            ->all();
        $about->save();
        ContentCache::flush();
    }

    public function down(): void
    {
    }
};
