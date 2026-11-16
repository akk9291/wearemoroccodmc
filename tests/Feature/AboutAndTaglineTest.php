<?php

namespace Tests\Feature;

use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AboutAndTaglineTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    public function test_header_shows_the_tagline_as_text_and_in_the_logo_alt(): void
    {
        $this->get('/')->assertOk()
            ->assertSee('<span class="brand-tag" aria-hidden="true">Destination Management Company</span>', false)
            ->assertSee('alt="We Are Morocco – Destination Management Company"', false)
            ->assertSee('<title>We Are Morocco | Destination Management Company in Morocco</title>', false);
    }

    public function test_about_page_has_the_client_copy_and_no_credentials(): void
    {
        $this->get('/about')->assertOk()
            ->assertSee('Making It Meaningful Is Our Craft.', false)
            ->assertSee('WE ARE MOROCCO is a Morocco-based Destination Management Company', false)
            ->assertSee('They are the ones that make you feel the most.', false)
            ->assertDontSee('Licensed, insured', false)
            ->assertDontSee('Licence number', false);
    }

    public function test_hidden_sections_are_kept_but_not_rendered(): void
    {
        $about = Page::where('slug', 'about')->first();
        $credentials = collect($about->sections)->first(fn ($b) => str_contains($b['html'] ?? '', 'Licensed, insured'));
        $this->assertTrue($credentials['hidden'] ?? false);

        $about->update(['sections' => collect($about->sections)->map(fn ($b) => array_diff_key($b, ['hidden' => 1]))->all()]);
        $this->get('/about')->assertSee('Licensed, insured', false);
    }
}
