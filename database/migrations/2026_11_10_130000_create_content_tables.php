<?php

use App\Support\ContentColumns as C;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pages', function (Blueprint $t) {
            $t->id();
            $t->string('title');
            $t->string('slug')->unique();
            $t->string('template')->default('default');
            C::hero($t);
            $t->json('sections')->nullable()->comment('ordered blocks: html | cta | script');
            C::seo($t);
            C::publishing($t);
        });

        Schema::create('services', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->string('slug')->unique();
            $t->text('eyebrow')->nullable();
            $t->text('intro')->nullable();
            C::hero($t);
            $t->json('highlights')->nullable();
            $t->json('sections')->nullable();
            $t->string('card_kicker')->nullable();
            $t->text('card_summary')->nullable();
            $t->string('card_image', 1024)->nullable();
            $t->string('card_image_alt')->nullable();
            C::seo($t);
            C::publishing($t);
        });

        Schema::create('destinations', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->string('slug')->unique();
            $t->string('region')->nullable();
            $t->string('best_season')->nullable();
            $t->string('best_for')->nullable();
            $t->text('summary')->nullable()->comment('"At a glance" card text');
            $t->text('intro')->nullable()->comment('featured card text');
            $t->string('tagline')->nullable()->comment('featured card kicker, e.g. The Red City');
            $t->string('featured_name')->nullable();
            $t->string('featured_image', 1024)->nullable();
            $t->string('featured_image_alt')->nullable();
            $t->unsignedInteger('featured_order')->nullable();
            $t->string('card_image', 1024)->nullable();
            $t->string('card_image_alt')->nullable();
            $t->boolean('has_page')->default(false)->comment('has its own /destinations/{slug} page');
            $t->boolean('is_featured')->default(false);
            C::hero($t);
            $t->json('highlights')->nullable();
            $t->json('gallery')->nullable();
            $t->json('sections')->nullable();
            C::seo($t);
            C::publishing($t);
        });

        Schema::create('itineraries', function (Blueprint $t) {
            $t->id();
            $t->string('title');
            $t->string('slug')->unique();
            $t->string('number', 10)->nullable()->comment('signature number, e.g. 01');
            $t->string('tagline')->nullable();
            $t->unsignedSmallInteger('days')->nullable();
            $t->unsignedSmallInteger('nights')->nullable();
            $t->string('duration_label')->nullable();
            $t->decimal('price_from', 10, 2)->nullable();
            $t->string('currency', 3)->default('EUR');
            $t->string('theme')->nullable();
            $t->string('route')->nullable()->comment('hero route');
            $t->string('card_route')->nullable()->comment('route shown on cards');
            $t->json('route_stops')->nullable();
            $t->text('summary')->nullable();
            $t->text('character')->nullable();
            $t->json('highlights')->nullable();
            $t->json('includes')->nullable();
            $t->json('excludes')->nullable();
            $t->json('gallery')->nullable();
            $t->boolean('is_signature')->default(false);
            $t->string('inquiry_key')->nullable();
            $t->string('filter_type')->nullable();
            $t->string('filter_dest')->nullable();
            $t->string('filter_len')->nullable();
            $t->string('card_image', 1024)->nullable();
            $t->string('card_image_alt')->nullable();
            $t->json('final_cta')->nullable();
            C::hero($t);
            $t->json('sections')->nullable();
            C::seo($t);
            C::publishing($t);
        });

        Schema::create('itinerary_days', function (Blueprint $t) {
            $t->id();
            $t->foreignId('itinerary_id')->constrained()->cascadeOnDelete();
            $t->unsignedSmallInteger('day_no');
            $t->string('title');
            $t->text('description')->nullable();
            $t->string('image', 1024)->nullable();
            $t->string('image_alt')->nullable();
            $t->string('overnight')->nullable();
            $t->string('meals')->nullable();
            $t->timestamps();
            $t->unique(['itinerary_id', 'day_no']);
        });

        Schema::create('destination_itinerary', function (Blueprint $t) {
            $t->foreignId('destination_id')->constrained()->cascadeOnDelete();
            $t->foreignId('itinerary_id')->constrained()->cascadeOnDelete();
            $t->primary(['destination_id', 'itinerary_id']);
        });

        Schema::create('experiences', function (Blueprint $t) {
            $t->id();
            $t->string('title');
            $t->string('slug')->unique();
            $t->string('category')->nullable()->comment('space separated filter values');
            $t->string('location')->nullable();
            $t->foreignId('destination_id')->nullable()->constrained()->nullOnDelete();
            $t->string('image', 1024)->nullable();
            $t->string('image_alt')->nullable();
            $t->text('description')->nullable();
            C::publishing($t);
        });

        Schema::create('case_studies', function (Blueprint $t) {
            $t->id();
            $t->string('client')->nullable();
            $t->string('title');
            $t->string('slug')->unique();
            $t->string('industry')->nullable();
            $t->string('group_size')->nullable();
            $t->string('location')->nullable();
            $t->string('year')->nullable();
            C::hero($t);
            $t->text('challenge')->nullable();
            $t->text('solution')->nullable();
            $t->json('results')->nullable();
            $t->json('gallery')->nullable();
            $t->longText('body')->nullable();
            $t->json('sections')->nullable();
            $t->string('card_kicker')->nullable();
            $t->string('card_title')->nullable();
            $t->text('card_summary')->nullable();
            $t->string('card_image', 1024)->nullable();
            $t->string('card_image_alt')->nullable();
            $t->string('filter_service')->nullable();
            C::seo($t);
            C::publishing($t);
        });

        Schema::create('journal_categories', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->string('slug')->unique();
            $t->unsignedInteger('sort_order')->default(0);
            $t->timestamps();
        });

        Schema::create('journal_posts', function (Blueprint $t) {
            $t->id();
            $t->foreignId('category_id')->nullable()->constrained('journal_categories')->nullOnDelete();
            $t->string('title');
            $t->string('slug')->unique();
            $t->text('excerpt')->nullable();
            $t->longText('body')->nullable();
            $t->string('cover_image', 1024)->nullable();
            $t->string('cover_image_alt')->nullable();
            $t->string('author')->nullable();
            $t->unsignedSmallInteger('reading_minutes')->nullable();
            $t->timestamp('published_at')->nullable();
            $t->string('published_label')->nullable()->comment('shown while published_at is a placeholder');
            $t->boolean('is_featured')->default(false);
            C::hero($t);
            $t->json('sections')->nullable();
            C::seo($t);
            C::publishing($t);
        });

        Schema::create('faqs', function (Blueprint $t) {
            $t->id();
            $t->text('question');
            $t->text('answer')->comment('HTML');
            $t->string('group')->index();
            C::publishing($t);
        });

        Schema::create('testimonials', function (Blueprint $t) {
            $t->id();
            $t->text('quote');
            $t->string('name');
            $t->string('role')->nullable();
            $t->string('company')->nullable();
            $t->string('trip')->nullable();
            $t->string('image', 1024)->nullable();
            $t->string('placement')->default('guests')->index();
            C::publishing($t);
        });

        Schema::create('partners', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->string('logo', 1024)->nullable();
            $t->string('url', 1024)->nullable();
            C::publishing($t);
        });
    }

    public function down(): void
    {
        foreach (['partners', 'testimonials', 'faqs', 'journal_posts', 'journal_categories', 'case_studies', 'experiences',
            'destination_itinerary', 'itinerary_days', 'itineraries', 'destinations', 'services', 'pages'] as $table) {
            Schema::dropIfExists($table);
        }
    }
};
