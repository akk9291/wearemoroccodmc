<?php

use App\Models\Page;
use App\Models\Setting;
use App\Support\ContentCache;
use Illuminate\Database\Migrations\Migration;

/**
 * Client copy for the About page (WE_ARE_MOROCCO_About_Us.pdf), the Credentials block hidden until the
 * credentials are confirmed, and "Destination Management Company" in the home / default SEO text.
 * Works on the existing rows: blocks are found by their eyebrow, anything an admin replaced is left alone.
 */
return new class extends Migration
{
    public const ABOUT_META = 'WE ARE MOROCCO is a Morocco-based Destination Management Company creating private, tailor-made journeys for discerning travelers through close collaboration with travel advisors.';

    public const HOME_TITLE = 'We Are Morocco | Destination Management Company in Morocco';

    public const HOME_META = 'We Are Morocco is a Morocco-based Destination Management Company creating private, tailor-made journeys, events and incentives, designed by local experts in Marrakech.';

    private const OLD_HOME_TITLE = 'We Are Morocco — Luxury Private Journeys';

    private const OLD_META = 'Tailor-made luxury travel in Morocco. Private riads, Sahara camps and exclusive access — designed by local experts in Marrakech with fifty years of combined experience.';

    private const OLD_ABOUT_META = 'A Marrakech atelier for private journeys and events: who we are, how we work and the team on the ground.';

    private const OLD_ABOUT_SUB = 'A destination atelier born and based in Marrakech, with partners in every region of the country and fifty years of combined work on this ground.';

    public function up(): void
    {
        $about = Page::withTrashed()->where('slug', 'about')->first();
        if ($about && ! str_contains(json_encode($about->sections), 'Making It Meaningful')) {
            $sections = [];
            foreach ($about->sections ?? [] as $block) {
                $eyebrow = $this->eyebrow($block);
                if ($eyebrow === 'Our story') {
                    $sections[] = ['type' => 'html', 'html' => static::intro()];
                } elseif ($eyebrow === 'Our mission') {
                    $sections[] = ['type' => 'html', 'html' => static::moreThanAnItinerary()];
                    $sections[] = ['type' => 'html', 'html' => static::partnersAndGuide()];
                } elseif ($eyebrow === 'Credentials') {
                    $sections[] = $block + ['hidden' => true];
                } elseif (($block['type'] ?? null) === 'cta') {
                    $sections[] = ['type' => 'html', 'html' => static::philosophy()];
                    $sections[] = $block;
                } else {
                    $sections[] = $block;
                }
            }
            if (! str_contains(json_encode($sections), 'Our philosophy')) {
                $sections[] = ['type' => 'html', 'html' => static::philosophy()];
            }
            $about->sections = $sections;
            if (in_array($about->meta_description, [null, '', self::OLD_ABOUT_META], true)) {
                $about->meta_description = self::ABOUT_META;
            }
            if (in_array($about->hero_subtitle, [null, '', self::OLD_ABOUT_SUB], true)) {
                $about->hero_subtitle = 'Private Morocco, curated for the well travelled.';
            }
            $about->save();
        }

        $home = Page::withTrashed()->where('slug', 'home')->first();
        if ($home) {
            if (in_array($home->meta_title, [null, '', self::OLD_HOME_TITLE], true)) {
                $home->meta_title = self::HOME_TITLE;
            }
            if (in_array($home->meta_description, [null, '', self::OLD_META], true)) {
                $home->meta_description = self::HOME_META;
            }
            $home->save();
        }

        $default = Setting::where('key', 'default_meta_description')->first();
        if ($default && in_array($default->value, [null, '', self::OLD_META], true)) {
            $default->update(['value' => self::HOME_META]);
        }

        ContentCache::flush();
    }

    public function down(): void
    {
        // Content change: restore earlier copy from the page history in the admin if needed.
    }

    private function eyebrow(array $block): ?string
    {
        return preg_match('/class="eyebrow">([^<]+)</', (string) ($block['html'] ?? ''), $m) ? trim($m[1]) : null;
    }

    public static function intro(): string
    {
        return <<<'HTML'
<section class="sec bg-parch">
  <div class="container cols c-1-1 items-center">
    <div class="feature reveal-left"><span class="ring" aria-hidden="true"></span><div class="frame img-zoom"><img src="https://images.pexels.com/photos/50630/pexels-photo-50630.jpeg?auto=compress&cs=tinysrgb&dpr=2&w=1600" alt="A guest looking out over Marrakech at dusk from a private terrace" loading="lazy" decoding="async"></div><div class="badge"><b>Your Morocco, your way.</b><span>Private Morocco, curated for the well travelled.</span></div></div>
    <div class="reveal-right"><p class="eyebrow">About us</p><h2 class="h2 big">Morocco Is Our Home. <span class="it">Making It Meaningful Is Our Craft.</span></h2>
      <p class="lead"><strong>WE ARE MOROCCO is a Morocco-based Destination Management Company creating private, tailor-made journeys for discerning travelers through close collaboration with travel advisors.</strong></p>
      <p class="lead">Morocco is a country of remarkable contrasts — imperial cities and hidden riads, ancient medinas and contemporary design, vast desert landscapes and mountain villages, centuries-old traditions and an evolving cultural scene.</p>
      <p class="lead">But experiencing Morocco well takes more than knowing where to go. It takes an understanding of its people, its rhythms, its cultural nuances, and the details that transform a visit into something genuinely memorable.</p>
      <p class="lead">That is where we come in.</p>
      <p class="lead">Rooted in Morocco and shaped by an intimate understanding of the destination, we bring together carefully selected accommodations, private transportation, knowledgeable local experts, meaningful cultural encounters, and thoughtful on-the-ground coordination to create journeys that feel personal from beginning to end.</p>
      <p class="lead">We do not believe in one way to experience Morocco. We believe in discovering the right Morocco for each traveler.</p>
      <div class="ctas"><button type="button" class="btn btn-gold " data-inquiry=""><span>Begin a conversation</span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg></button><a class="btn btn-outline" href="/why-us">Why travelers choose us</a></div></div>
  </div>
</section>
HTML;
    }

    public static function moreThanAnItinerary(): string
    {
        return <<<'HTML'
<section class="sec bg-ink center">
  <span class="orb orb-soft" aria-hidden="true"></span>
  <div class="container narrow reveal"><p class="eyebrow">Our approach</p><h2 class="h2 big" style="margin-top:1.25rem">More Than <span class="it">an Itinerary.</span></h2>
    <p class="lead">We see ourselves as the people behind the experience — connecting the right places, people, and moments to create a journey that feels effortless without losing its sense of discovery.</p>
    <p class="lead">Our approach combines local knowledge with careful planning and personal attention. Every detail has a purpose, every recommendation is considered, and every journey is designed around the people taking it.</p>
    <p class="lead">Whether the priority is cultural discovery, exceptional hospitality, time in the mountains, a private Sahara experience, or simply the freedom to explore at a comfortable pace, we shape the journey around what matters most to the traveler.</p>
    <p class="lead">Our signature journeys offer inspiration and a starting point. Our tailor-made approach allows us to go further, adapting the route, rhythm, accommodations, and experiences to each client's individual interests and expectations.</p></div>
</section>
HTML;
    }

    public static function partnersAndGuide(): string
    {
        return <<<'HTML'
<section class="sec bg-parch">
  <div class="container"><div class="grid g-2" style="gap:1.5rem">
    <article class="vcard reveal"><p class="kicker">For travel advisors</p><h3 class="h3" style="margin-top:.75rem">A Trusted Partner <span class="it">for Travel Advisors</span></h3>
      <p style="margin-top:1.25rem">We work alongside travel advisors who want to offer their clients a deeper, more personal experience of Morocco.</p>
      <p style="margin-top:1rem">You bring your understanding of the traveler. We bring our destination expertise, local relationships, and operational knowledge.</p>
      <p style="margin-top:1rem">Together, we develop journeys that reflect the client's expectations while providing the reassurance of a dedicated team on the ground.</p>
      <p style="margin-top:1rem">From the initial itinerary design to the coordination of the journey itself, we believe in clear communication, thoughtful recommendations, and a collaborative approach. Our role is to support your relationship with your client — never replace it.</p></article>
    <article class="vcard reveal" style="transition-delay:80ms"><p class="kicker">The Guardian Guide</p><h3 class="h3" style="margin-top:.75rem">A Familiar Face <span class="it">Throughout the Journey</span></h3>
      <p style="margin-top:1.25rem">At the heart of our journeys is the <strong>Guardian Guide</strong>: a dedicated English-speaking host who accompanies guests throughout their private journey.</p>
      <p style="margin-top:1rem">More than a traditional guide, the Guardian Guide is a cultural interpreter, trusted local contact, and personal advisor — someone who helps travelers understand Morocco beyond the familiar landmarks.</p>
      <p style="margin-top:1rem">This continuity brings a human dimension to the journey. It allows recommendations to reflect the guests' evolving interests and gives them the confidence to explore with someone who understands both the destination and their preferences.</p></article>
  </div></div>
</section>
HTML;
    }

    public static function philosophy(): string
    {
        return <<<'HTML'
<section class="sec bg-ink center">
  <span class="orb orb-soft" aria-hidden="true"></span>
  <div class="container narrow reveal"><p class="eyebrow">Our philosophy</p><h2 class="h2 big" style="margin-top:1.25rem">Your Morocco, <span class="it">Your Way.</span></h2>
    <p class="lead">We believe luxury is not simply about adding more. It is about choosing what is right for each traveler.</p>
    <p class="lead">The right pace. The right places. The right experiences. The freedom to change direction when curiosity leads somewhere unexpected.</p>
    <p class="lead">We remain attentive to the details while leaving room for spontaneity, discovery, and genuine human connection.</p>
    <p class="lead"><em>Because the most memorable journeys are not necessarily the ones that do the most. They are the ones that make you feel the most.</em></p></div>
</section>
HTML;
    }
};
