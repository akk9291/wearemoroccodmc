<?php

namespace Tests\Feature;

use App\Filament\Resources\ExperienceResource;
use App\Models;
use App\Support\ImageVariants;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class ImagesAndAssetsTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    protected function setUp(): void
    {
        parent::setUp();
        config(['livewire.inject_assets' => false]);
    }

    public function test_upload_in_the_admin_creates_webp_variants_used_in_srcset(): void
    {
        Storage::fake('public');
        $editor = Models\User::factory()->create();
        $editor->assignRole('Editor');
        $this->actingAs($editor);

        Livewire::test(ExperienceResource\Pages\EditExperience::class, ['record' => 'private-dinner-in-the-desert'])
            ->set('data.image__upload', [UploadedFile::fake()->image('dinner.jpg', 2000, 1500)])
            ->call('save')->assertHasNoFormErrors();

        $path = Models\Experience::where('slug', 'private-dinner-in-the-desert')->value('image');
        $disk = Storage::disk('public');
        foreach ([null, 800, 1600] as $w) {
            $disk->assertExists(ImageVariants::variantPath($path, $w));
        }
        $this->assertSame([800, 600], array_slice(getimagesize($disk->path(ImageVariants::variantPath($path, 800))), 0, 2));
        $this->assertSame('image/webp', getimagesize($disk->path(ImageVariants::variantPath($path, 1600)))['mime']);

        auth()->logout();
        $html = $this->get('/experiences')->assertOk()->getContent();
        $this->assertStringContainsString('src="'.url($disk->url($path)).'"', $html);
        $this->assertStringContainsString(url($disk->url(ImageVariants::variantPath($path, 800))).' 800w', $html);
        $this->assertStringContainsString(url($disk->url(ImageVariants::variantPath($path))).' 2000w', $html);
    }

    public function test_small_images_are_not_upscaled_and_the_backfill_command_is_idempotent(): void
    {
        Storage::fake('public');
        Storage::disk('public')->putFileAs('uploads', UploadedFile::fake()->image('small.png', 700, 400), 'small.png');
        Storage::disk('public')->putFileAs('uploads', UploadedFile::fake()->image('big.jpg', 1800, 1000), 'big.jpg');

        $this->assertSame(0, Artisan::call('images:variants'));
        $disk = Storage::disk('public');
        $disk->assertExists(['uploads/small.webp', 'uploads/big.webp', 'uploads/big-800.webp', 'uploads/big-1600.webp']);
        $disk->assertMissing(['uploads/small-800.webp', 'uploads/small-1600.webp']);

        Artisan::call('images:variants');
        $this->assertStringContainsString('0 variant(s) written', Artisan::output());
    }

    public function test_pexels_images_get_a_srcset_of_smaller_widths(): void
    {
        $set = ImageVariants::pexelsSrcset('https://images.pexels.com/photos/1/pexels-photo-1.jpeg?auto=compress&cs=tinysrgb&dpr=2&w=800');
        $this->assertSame([480, 800, 1200, 1600], array_map(fn ($c) => (int) explode(' ', $c)[1], explode(', ', $set)));
        $this->assertStringContainsString('dpr=1&w=480 480w', $set);
        $this->assertNull(ImageVariants::srcset('https://example.com/photo.jpg'));
    }

    public function test_stored_html_images_get_lazy_loading_and_srcset_without_breaking_swapped_or_hero_images(): void
    {
        $pexels = 'https://images.pexels.com/photos/1/p.jpeg?auto=compress&amp;cs=tinysrgb&amp;dpr=2&amp;w=1600';
        $html = ImageVariants::enhanceHtml('<img src="'.$pexels.'" alt="a"><img src="'.$pexels.'" fetchpriority="high" alt="hero">'
            .'<img id="s-img" src="'.$pexels.'" alt="swapped"><img src="/x.jpg" loading="eager" alt="kept">');

        preg_match_all('#<img[^>]*>#', $html, $m);
        [$card, $hero, $swapped, $kept] = $m[0];
        $this->assertStringContainsString('loading="lazy"', $card);
        $this->assertStringContainsString('srcset="', $card);
        $this->assertStringNotContainsString('loading=', $hero);
        $this->assertStringContainsString('srcset="', $hero);
        $this->assertStringNotContainsString('srcset', $swapped, 'arena.js swaps its src');
        $this->assertStringContainsString('loading="eager"', $kept);
        $this->assertStringNotContainsString('loading="lazy"', $kept);
    }

    public function test_pages_use_responsive_images_versioned_assets_and_deferred_scripts(): void
    {
        $html = $this->get('/itineraries/grand-tour')->assertOk()->getContent();

        // hero: high priority, srcset, never lazy
        $this->assertMatchesRegularExpression('#<img (?=[^>]*fetchpriority="high")(?=[^>]*srcset="[^"]+ 480w)(?![^>]*loading=)[^>]*class="kenburns"#', $html);
        // every other image is lazy
        preg_match_all('#<img\b[^>]*>#', $html, $imgs);
        foreach ($imgs[0] as $img) {
            if (! str_contains($img, 'fetchpriority')) {
                $this->assertStringContainsString('loading="lazy"', $img, $img);
            }
        }
        foreach (['arena.css', 'arena.js', 'forms.js'] as $file) {
            $this->assertMatchesRegularExpression('#assets/'.preg_quote($file).'\?v=\d+#', $html);
        }
        $this->assertMatchesRegularExpression('#<script src="[^"]+arena\.js\?v=\d+" defer></script>\s*<script src="[^"]+forms\.js\?v=\d+" defer></script>#', $html);
        $this->assertStringContainsString('<link rel="preconnect" href="https://images.pexels.com">', $html);
        $this->assertStringContainsString('<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>', $html);
        $this->assertStringContainsString('media="print" onload="this.media=\'all\'"', $html);
    }
}
