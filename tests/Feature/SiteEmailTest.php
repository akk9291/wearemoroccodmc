<?php

namespace Tests\Feature;

use App\Models;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/** The atelier email is info@wearemoroccodmc.com everywhere. */
class SiteEmailTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    private const OLD = 'info@wearemoroccotravel.com';

    private const NEW = 'info@wearemoroccodmc.com';

    public function test_pages_show_the_new_email_only(): void
    {
        foreach (['/', '/contact', '/faq', '/privacy'] as $url) {
            $html = $this->get($url)->assertOk()->getContent();
            $this->assertStringNotContainsString('wearemoroccotravel', $html, $url);
        }
        $this->assertStringContainsString('mailto:'.self::NEW, $this->get('/contact')->getContent());
    }

    public function test_migration_moves_content_settings_and_admin_login_to_the_new_email(): void
    {
        $about = Models\Page::where('slug', 'about')->first();
        $about->meta_description = 'Write to '.self::OLD;
        $about->save();
        DB::table('settings')->where('value', self::NEW)->update(['value' => self::OLD]);
        $admin = Models\User::factory()->create(['email' => self::OLD]);

        (require database_path('migrations/2026_11_17_120000_email_on_wearemoroccodmc_domain.php'))->up();

        $this->assertSame('Write to '.self::NEW, Models\Page::where('slug', 'about')->value('meta_description'));
        $this->assertSame(0, DB::table('settings')->where('value', 'like', '%wearemoroccotravel%')->count());
        $this->assertSame(self::NEW, $admin->fresh()->email);
    }
}
