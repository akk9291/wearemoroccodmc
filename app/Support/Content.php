<?php

namespace App\Support;

use App\Models\Setting;
use Illuminate\Support\Facades\View;

/**
 * Renders HTML stored in the database:
 *  - root-relative links (href="/services/events") become absolute URLs for the current host,
 *  - <!--wam:block key="value"--> markers are replaced by resources/views/blocks/{block}.blade.php,
 *    which render repeating lists (itineraries, FAQs, ...) from their tables.
 */
class Content
{
    public static function render(?string $html): string
    {
        if ($html === null || $html === '') {
            return '';
        }

        $html = preg_replace_callback('/<!--wam:([a-z-]+)((?: [a-z]+="[^"]*")*)-->/', function ($m) {
            preg_match_all('/ ([a-z]+)="([^"]*)"/', $m[2], $pairs, PREG_SET_ORDER);
            $args = [];
            foreach ($pairs as [, $key, $value]) {
                $args[$key] = $value;
            }
            $view = 'blocks.'.$m[1];
            abort_unless(View::exists($view), 500, "Unknown content block {$m[1]}");

            return trim(view($view, $args)->render(), "\n");
        }, $html);

        return static::urls($html);
    }

    /** href="/x" and src="/x" -> absolute URL; protocol-relative (//) and other URLs untouched. */
    public static function urls(string $html): string
    {
        return preg_replace_callback('/\b(href|src)="(\/(?!\/)[^"#]*)(#[^"]*)?"/', function ($m) {
            return $m[1].'="'.url($m[2]).($m[3] ?? '').'"';
        }, $html);
    }

    /** Breadcrumb <li> items from [{label, url|null}]. */
    public static function crumbs(?array $crumbs): string
    {
        return collect($crumbs ?? [])->map(fn ($c) => $c['url']
            ? '<li><a href="'.e(url($c['url'])).'">'.e($c['label']).'</a></li>'
            : '<li aria-current="page">'.e($c['label']).'</li>')->implode('');
    }

    /** Shared inline SVG icons (arrow, star, check, doc). */
    public static function icon(string $name): string
    {
        return (string) (Setting::get('icons')[$name] ?? '');
    }
}
