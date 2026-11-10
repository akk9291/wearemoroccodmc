<?php

namespace Database\Seeders;

use App\Models\CaseStudy;
use App\Models\Destination;
use App\Models\Experience;
use App\Models\Faq;
use App\Models\Itinerary;
use App\Models\JournalCategory;
use App\Models\JournalPost;
use App\Models\Page;
use App\Models\Service;
use App\Models\Testimonial;
use Illuminate\Database\Seeder;

/** The site's real content, extracted from the original static pages. Safe to re-run (upserts by slug). */
class ContentSeeder extends Seeder
{
    use ReadsSeedData;

    public function run(): void
    {
        foreach ($this->data('pages') as $row) {
            Page::withTrashed()->updateOrCreate(['slug' => $row['slug']], $this->only(Page::class, $row));
        }

        foreach ($this->data('services') as $row) {
            Service::withTrashed()->updateOrCreate(['slug' => $row['slug']], $this->only(Service::class, $row));
        }

        $destinations = [];
        foreach ($this->data('destinations') as $row) {
            $destinations[$row['slug']] = Destination::withTrashed()
                ->updateOrCreate(['slug' => $row['slug']], $this->only(Destination::class, $row));
        }

        foreach ($this->data('itineraries') as $row) {
            $itinerary = Itinerary::withTrashed()->updateOrCreate(['slug' => $row['slug']], $this->only(Itinerary::class, $row));
            $itinerary->itineraryDays()->delete();
            $itinerary->itineraryDays()->createMany($row['days_list']);
            $itinerary->destinations()->sync(collect($row['destinations'])->map(fn ($s) => $destinations[$s]->id));
        }

        foreach ($this->data('experiences') as $row) {
            Experience::withTrashed()->updateOrCreate(['slug' => $row['slug']], $this->only(Experience::class, $row));
        }

        foreach ($this->data('case_studies') as $row) {
            CaseStudy::withTrashed()->updateOrCreate(['slug' => $row['slug']], $this->only(CaseStudy::class, $row));
        }

        $categories = [];
        foreach ($this->data('journal_categories') as $row) {
            $categories[$row['slug']] = JournalCategory::updateOrCreate(['slug' => $row['slug']], $row);
        }
        foreach ($this->data('journal_posts') as $row) {
            JournalPost::withTrashed()->updateOrCreate(
                ['slug' => $row['slug']],
                $this->only(JournalPost::class, $row) + ['category_id' => $categories[$row['category']]->id],
            );
        }

        Faq::query()->forceDelete();
        foreach ($this->data('faqs') as $row) {
            Faq::create($row);
        }

        Testimonial::query()->forceDelete();
        foreach ($this->data('testimonials') as $row) {
            Testimonial::create($row);
        }
    }
}
