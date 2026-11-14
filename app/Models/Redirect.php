<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/** URL redirects handled by App\Http\Middleware\HandleRedirects (cached path map). */
class Redirect extends Model
{
    public const CACHE_KEY = 'redirects.map';

    public const CODES = [301 => '301 Permanent', 302 => '302 Temporary'];

    public const SOURCES = ['manual' => 'Added in admin', 'legacy' => 'Old .html address', 'slug' => 'Slug change'];

    protected $fillable = ['from_path', 'to_path', 'status_code', 'hits', 'last_hit_at', 'source'];

    protected $casts = ['last_hit_at' => 'datetime', 'status_code' => 'integer', 'hits' => 'integer'];

    protected static function booted(): void
    {
        static::saving(function (Redirect $r) {
            $r->from_path = static::normalize($r->from_path);
            if (! preg_match('#^https?://#i', (string) $r->to_path)) {
                $r->to_path = static::normalize($r->to_path);
            }
        });
        static::saved(fn () => Cache::forget(self::CACHE_KEY));
        static::deleted(fn () => Cache::forget(self::CACHE_KEY));
    }

    /** "about.html", "/About/", "https://host/x?y" -> "/about.html", "/About", "/x" */
    public static function normalize(?string $path): string
    {
        $path = (string) (parse_url(trim((string) $path), PHP_URL_PATH) ?? '');

        return rawurldecode('/'.trim($path, '/'));
    }

    /** @return array<string, array{0: string, 1: int}> from_path => [to_path, status] */
    public static function map(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, fn () => static::query()->get(['from_path', 'to_path', 'status_code'])
            ->mapWithKeys(fn ($r) => [$r->from_path => [$r->to_path, $r->status_code]])->all());
    }

    /**
     * Send $from to $to: replaces an existing redirect from $from, re-points redirects that led to $from (no chains)
     * and drops a redirect away from $to (no loops).
     */
    public static function point(string $from, string $to, string $source = 'slug', int $status = 301): void
    {
        $from = static::normalize($from);
        $to = static::normalize($to);
        if ($from === $to) {
            return;
        }
        static::where('from_path', $to)->delete();
        static::where('to_path', $from)->get()->each(fn ($r) => $r->update(['to_path' => $to]));
        static::updateOrCreate(['from_path' => $from], ['to_path' => $to, 'status_code' => $status, 'source' => $source]);
    }
}
