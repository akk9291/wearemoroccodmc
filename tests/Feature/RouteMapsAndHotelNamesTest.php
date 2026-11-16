<?php

namespace Tests\Feature;

use App\Models\Faq;
use App\Models\Itinerary;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RouteMapsAndHotelNamesTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    private const BRANDS = ['Royal Mansour', 'Mamounia', 'Dar Ahlam', 'Oberoi', 'Mandarin Oriental'];

    public function test_itineraries_with_a_route_map_show_a_responsive_picture(): void
    {
        foreach (['grand-tour' => 'route-map-01-ifrane-midelt', 'magical-morocco' => 'route-map-02-four-routes', 'imperial-cities' => 'route-map-02-four-routes'] as $slug => $file) {
            $this->get("/itineraries/{$slug}")->assertOk()
                ->assertSee('class="route-map', false)
                ->assertSee("{$file}-800.webp 800w", false)
                ->assertSee("{$file}-2400.webp 2400w", false)
                ->assertSee('href="'.url("/assets/img/maps/{$file}-2400.jpg").'"', false)
                ->assertSee('alt="Route map of', false);
        }
        foreach (['desert-kasbahs', 'marrakech-incentive'] as $slug) {
            $this->get("/itineraries/{$slug}")->assertOk()->assertDontSee('class="route-map', false);
        }
    }

    public function test_the_map_files_exist(): void
    {
        foreach (Itinerary::whereNotNull('route_map')->pluck('route_map') as $map) {
            $this->assertFileExists(public_path(ltrim($map, '/')));
            $this->assertFileExists(public_path(str_replace('-2400.jpg', '-800.webp', ltrim($map, '/'))));
        }
    }

    public function test_no_hotel_brand_names_on_the_site(): void
    {
        foreach (['/', '/about', '/why-us', '/quote', '/destinations/marrakech', '/faq', '/itineraries/grand-tour'] as $path) {
            $html = $this->get($path)->getContent();
            foreach (self::BRANDS as $brand) {
                $this->assertStringNotContainsString($brand, $html, "{$brand} on {$path}");
            }
        }
    }

    public function test_the_command_replaces_names_in_existing_rows_and_is_idempotent(): void
    {
        $faq = Faq::first();
        $faq->update(['answer' => '<p>We love Royal Mansour, La Mamounia and Dar Ahlam.</p>']);

        $this->artisan('content:remove-hotel-names')->assertSuccessful();
        $this->assertSame('<p>We love Marrakech properties, Marrakech properties and Skoura properties.</p>', $faq->fresh()->answer);

        $this->artisan('content:remove-hotel-names')->expectsOutputToContain('Rows updated: 0')->assertSuccessful();
    }
}
