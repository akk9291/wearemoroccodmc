<?php

namespace Tests\Feature;

use App\Models;
use App\Support\ImageVariants;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Agafay is a stone desert: its places use the self-hosted Agafay photo, not the Merzouga dunes. */
class AgafayPhotoTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    private const NEW = '/assets/img/photos/agafay-desert-camp-1600.webp';

    private const DUNES = 'photos/29107889/';

    public function test_bundled_photos_get_a_srcset_of_their_webp_widths(): void
    {
        foreach ([800, 1600, 2400] as $w) {
            $this->assertFileExists(public_path("assets/img/photos/agafay-desert-camp-{$w}.webp"));
        }
        $set = ImageVariants::srcset(self::NEW);
        $this->assertSame([800, 1600, 2400], array_map(fn ($c) => (int) explode(' ', $c)[1], explode(', ', $set)));
        $this->assertStringContainsString(asset('assets/img/photos/agafay-desert-camp-800.webp').' 800w', $set);
        $this->assertNull(ImageVariants::srcset('/assets/img/photos/missing-photo-1600.webp'));
    }

    public function test_agafay_places_show_the_stone_desert_photo(): void
    {
        $card = $this->agafayCard($this->get('/destinations')->assertOk()->getContent());
        $this->assertStringContainsString('src="'.url(self::NEW).'"', $card);
        $this->assertStringContainsString('agafay-desert-camp-2400.webp 2400w', $card);
        $this->assertStringContainsString('alt="Stony hills of the Agafay desert', $card);
        // the #slug jump target is the card itself (no empty anchor taking a grid slot); 8rem = header room + the 2rem reveal offset
        $this->assertStringContainsString('scroll-margin-top:8rem', $card);
        $this->assertStringNotContainsString('top:-6rem', $this->get('/destinations')->getContent());

        $this->assertStringNotContainsString(self::DUNES, $this->get('/case-studies/agafay-gala')->assertOk()->getContent());

        foreach (['/experiences', '/destinations/marrakech', '/services/incentives', '/itineraries/marrakech-incentive', '/itineraries/magical-morocco'] as $url) {
            $html = $this->get($url)->assertOk()->getContent();
            $this->assertStringContainsString('agafay-desert-camp-1600.webp', $html, $url);
            $this->assertDoesNotMatchRegularExpression('#<img[^>]*'.preg_quote(self::DUNES, '#').'[^>]*alt="[^"]*Agafay#', $html, $url);
        }
        $day = Models\ItineraryDay::where('title', 'Agafay')->first();
        $this->assertSame(self::NEW, $day->image);
    }

    public function test_migration_swaps_existing_rows_and_keeps_admin_edits(): void
    {
        $old = 'https://images.pexels.com/photos/29107889/pexels-photo-29107889.jpeg?auto=compress&cs=tinysrgb&dpr=2&w=1600';
        $oldAlt = 'Golden Sahara dunes at sunset in Merzouga';
        Models\Destination::where('slug', 'agafay')->update(['card_image' => $old, 'card_image_alt' => $oldAlt]);
        Models\Experience::where('slug', 'private-dinner-in-the-desert')->update(['image' => 'uploads/own.jpg']);
        $marrakech = Models\Destination::where('slug', 'marrakech')->first();
        $marrakech->sections = collect($marrakech->sections)->map(fn ($b) => isset($b['html'])
            ? ['html' => str_replace(self::NEW, $old, str_replace('Stony hills of the Agafay desert with white camp tents and the snow-capped Atlas behind', $oldAlt, $b['html']))] + $b
            : $b)->all();
        $marrakech->save();

        (require database_path('migrations/2026_11_17_100000_agafay_stone_desert_photo.php'))->up();

        $agafay = Models\Destination::where('slug', 'agafay')->first();
        $this->assertSame(self::NEW, $agafay->card_image);
        $this->assertStringStartsWith('Stony hills', $agafay->card_image_alt);
        $this->assertSame('uploads/own.jpg', Models\Experience::where('slug', 'private-dinner-in-the-desert')->value('image'));
        $sections = json_encode(Models\Destination::where('slug', 'marrakech')->first()->sections, JSON_UNESCAPED_SLASHES);
        $this->assertStringContainsString('src=\"'.self::NEW.'\"', $sections);
        // the generic CTA band on the Marrakech page keeps the dunes photo
        $this->assertStringContainsString(self::DUNES, $sections);
    }

    private function agafayCard(string $html): string
    {
        $this->assertMatchesRegularExpression('#<a id="agafay"[^>]*>.*?</a>#s', $html);
        preg_match('#<a id="agafay"[^>]*>.*?</a>#s', $html, $m);

        return $m[0];
    }
}
