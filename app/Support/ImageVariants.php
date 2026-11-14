<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

/**
 * Responsive images.
 *  - Uploads (public disk): on upload a WebP copy plus 800 and 1600 px wide WebP variants are written next to the
 *    original (uploads/abc.jpg -> uploads/abc.webp, abc-800.webp, abc-1600.webp). `php artisan images:variants` backfills.
 *  - Pexels URLs: the CDN resizes on request, so the srcset varies its w= parameter.
 */
class ImageVariants
{
    public const WIDTHS = [800, 1600];

    public const PEXELS_WIDTHS = [480, 800, 1200, 1600, 2000, 2400];

    public const QUALITY = 80;

    public static function variantPath(string $path, ?int $width = null): string
    {
        $info = pathinfo($path);
        $dir = ($info['dirname'] ?? '.') === '.' ? '' : $info['dirname'].'/';

        return $dir.$info['filename'].($width ? '-'.$width : '').'.webp';
    }

    /** Writes the missing WebP files for an uploaded image. Returns the paths written. */
    public static function generate(string $path): array
    {
        $disk = Storage::disk('public');
        if (! static::isUpload($path) || ! $disk->exists($path) || ! function_exists('imagewebp')) {
            return [];
        }
        $source = @imagecreatefromstring($disk->get($path));
        if (! $source) {
            return [];
        }
        imagepalettetotruecolor($source);
        imagealphablending($source, true);
        imagesavealpha($source, true);

        $written = [];
        $width = imagesx($source);
        foreach ([null, ...self::WIDTHS] as $target) {
            if ($target !== null && $target >= $width) {
                continue; // never upscale
            }
            $out = static::variantPath($path, $target);
            if ($out === $path || $disk->exists($out)) {
                continue;
            }
            $image = $target ? imagescale($source, $target) : $source;
            ob_start();
            imagewebp($image, null, self::QUALITY);
            $disk->put($out, ob_get_clean());
            if ($image !== $source) {
                imagedestroy($image);
            }
            $written[] = $out;
        }
        imagedestroy($source);

        return $written;
    }

    /** ' srcset="..." sizes="..."' for an image column value, or '' when there is nothing better than src. */
    public static function attributes(?string $value, string $sizes = '100vw'): string
    {
        $srcset = static::srcset($value);

        return $srcset ? ' srcset="'.e($srcset).'" sizes="'.e($sizes).'"' : '';
    }

    public static function srcset(?string $value): ?string
    {
        if (! $value) {
            return null;
        }
        if (static::isPexels($value)) {
            return static::pexelsSrcset($value);
        }
        if (! static::isUpload($value)) {
            return null;
        }
        $disk = Storage::disk('public');
        $set = [];
        foreach (self::WIDTHS as $w) {
            if ($disk->exists($p = static::variantPath($value, $w))) {
                $set[] = url($disk->url($p)).' '.$w.'w';
            }
        }
        if ($set && $disk->exists($full = static::variantPath($value))) {
            $size = @getimagesize($disk->path($full));
            if ($size) {
                $set[] = url($disk->url($full)).' '.$size[0].'w';
            }
        }

        return $set ? implode(', ', $set) : null;
    }

    public static function isPexels(string $url): bool
    {
        return (bool) preg_match('#^https://images\.pexels\.com/photos/#', $url);
    }

    public static function isUpload(string $value): bool
    {
        return ! preg_match('#^(https?:)?//#i', $value) && ! str_starts_with($value, '/')
            && (bool) preg_match('/\.(jpe?g|png|webp|gif)$/i', $value);
    }

    /** Same photo at several widths (dpr=1), up to the width the original URL asked for. */
    public static function pexelsSrcset(string $url): ?string
    {
        $parts = parse_url(html_entity_decode($url));
        parse_str($parts['query'] ?? '', $query);
        $max = (int) ($query['w'] ?? 0) * max(1, (int) ($query['dpr'] ?? 1));
        if ($max <= 0) {
            return null;
        }
        $base = $parts['scheme'].'://'.$parts['host'].$parts['path'];
        $set = [];
        foreach (self::PEXELS_WIDTHS as $w) {
            if ($w <= $max) {
                $set[] = $base.'?'.http_build_query(['auto' => 'compress', 'cs' => 'tinysrgb', 'dpr' => 1, 'w' => $w]).' '.$w.'w';
            }
        }

        return count($set) > 1 ? implode(', ', $set) : null;
    }

    /**
     * Stored HTML: every <img> gets lazy loading + async decoding (unless it is the high-priority hero) and,
     * for Pexels photos, a srcset. Existing attributes are kept.
     */
    public static function enhanceHtml(string $html, string $sizes = '(min-width: 1024px) 60vw, 100vw'): string
    {
        return preg_replace_callback('#<img\b[^>]*>#i', function ($m) use ($sizes) {
            $tag = $m[0];
            $add = '';
            if (! preg_match('/\sloading=/i', $tag) && ! preg_match('/\sfetchpriority=/i', $tag)) {
                $add .= ' loading="lazy"';
            }
            if (! preg_match('/\sdecoding=/i', $tag)) {
                $add .= ' decoding="async"';
            }
            // images with an id are swapped by arena.js (src only), so they keep a plain src
            if (! preg_match('/\ssrcset=/i', $tag) && ! preg_match('/\sid=/i', $tag) && preg_match('/\ssrc="([^"]+)"/i', $tag, $src)) {
                $add .= static::attributes(html_entity_decode($src[1]), $sizes);
            }

            return $add === '' ? $tag : preg_replace('#\s*/?>$#', $add.'>', $tag, 1);
        }, $html);
    }
}
