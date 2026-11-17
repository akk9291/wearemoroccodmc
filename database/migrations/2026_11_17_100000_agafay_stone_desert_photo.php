<?php

use App\Models\CaseStudy;
use App\Models\Destination;
use App\Models\Experience;
use App\Models\Itinerary;
use App\Models\ItineraryDay;
use App\Models\Service;
use App\Support\ContentCache;
use Illuminate\Database\Migrations\Migration;

/**
 * Agafay is a stone desert, not dunes. Every place that shows Agafay used a Merzouga dunes photo (Pexels 29107889);
 * it now uses a self-hosted Agafay photo (Pexels 35910047, Othmane Ettalbi): stony hills, white camps, the Atlas behind.
 * Only the Agafay places change. The generic "Sahara dunes" CTA bands elsewhere keep their photo.
 * Values an admin already replaced are left alone.
 */
return new class extends Migration
{
    public const OLD = 'https://images.pexels.com/photos/29107889/pexels-photo-29107889.jpeg?auto=compress&cs=tinysrgb&dpr=2&w=1600';

    public const OLD_ALT = 'Golden Sahara dunes at sunset in Merzouga';

    public const NEW = '/assets/img/photos/agafay-desert-camp-1600.webp';

    public const NEW_ALT = 'Stony hills of the Agafay desert with white camp tents and the snow-capped Atlas behind';

    public function up(): void
    {
        $this->swapRow(Destination::class, 'agafay', ['card_image', 'featured_image', 'hero_image', 'og_image']);
        $this->swapRow(Destination::class, 'marrakech', ['sections'], htmlOnly: true);        // "Experiences here": private dinner in Agafay
        $this->swapRow(CaseStudy::class, 'agafay-gala', ['hero_image', 'card_image', 'sections', 'gallery']);
        $this->swapRow(Experience::class, 'private-dinner-in-the-desert', ['image']);
        $this->swapRow(Service::class, 'incentives', ['sections'], htmlOnly: true);           // day 03: desert gala in Agafay
        $this->swapRow(Service::class, 'events', ['sections'], htmlOnly: true);               // "Gala dinners" card -> Agafay gala case study
        $this->swapRow(Itinerary::class, 'magical-morocco', ['gallery']);                     // route has Agafay, no Sahara
        $this->swapRow(Itinerary::class, 'marrakech-incentive', ['gallery']);

        $days = ['magical-morocco' => 'Marrakech → Agafay', 'marrakech-incentive' => 'Agafay'];
        foreach ($days as $slug => $title) {
            $id = Itinerary::withoutGlobalScopes()->where('slug', $slug)->value('id');
            if (! $id) {
                continue;
            }
            foreach (ItineraryDay::where('itinerary_id', $id)->where('title', $title)->where('image', self::OLD)->get() as $day) {
                $day->image = self::NEW;
                if (in_array($day->image_alt, [null, '', self::OLD_ALT], true)) {
                    $day->image_alt = self::NEW_ALT;
                }
                $day->save();
            }
        }

        ContentCache::flush();
    }

    public function down(): void
    {
    }

    /** @param class-string<\Illuminate\Database\Eloquent\Model> $model */
    private function swapRow(string $model, string $slug, array $columns, bool $htmlOnly = false): void
    {
        $row = $model::withoutGlobalScopes()->where('slug', $slug)->first();
        if (! $row) {
            return;
        }
        foreach ($columns as $column) {
            $value = $row->getAttribute($column);
            if ($value === null) {
                continue;
            }
            if (is_array($value)) {
                $value = array_map(fn ($block) => $htmlOnly && ($block['type'] ?? null) !== 'html' ? $block : static::swap($block), $value);
            } elseif ($value === self::OLD) {
                $alt = $column.'_alt';
                if (array_key_exists($alt, $row->getAttributes()) && in_array($row->getAttribute($alt), [null, '', self::OLD_ALT], true)) {
                    $row->setAttribute($alt, self::NEW_ALT);
                }
                $value = self::NEW;
            }
            $row->setAttribute($column, $value);
        }
        $row->save();
    }

    /** Swaps the photo in a block / gallery item (image + its alt) or in an HTML string (<img> src + alt). */
    public static function swap(mixed $value): mixed
    {
        if (is_string($value)) {
            return preg_replace_callback('#<img\b[^>]*>#i', function ($m) {
                if (! str_contains($m[0], 'photos/29107889/')) {
                    return $m[0];
                }

                return preg_replace(['#\ssrc="[^"]*"#i', '#\salt="'.preg_quote(self::OLD_ALT, '#').'"#i'],
                    [' src="'.self::NEW.'"', ' alt="'.self::NEW_ALT.'"'], $m[0]);
            }, $value);
        }
        if (! is_array($value)) {
            return $value;
        }
        foreach ($value as $key => $item) {
            if ($item === self::OLD && is_string($key)) {
                $value[$key] = self::NEW;
                foreach ([$key.'_alt', 'alt'] as $altKey) {
                    if (($value[$altKey] ?? null) === self::OLD_ALT) {
                        $value[$altKey] = self::NEW_ALT;
                    }
                }
            } elseif (is_array($item) || is_string($item)) {
                $value[$key] = static::swap($item);
            }
        }

        return $value;
    }
};
