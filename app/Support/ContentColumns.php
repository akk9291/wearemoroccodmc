<?php

namespace App\Support;

use Illuminate\Database\Schema\Blueprint;

/** Column sets shared by the content tables (used by the migrations). */
class ContentColumns
{
    /** Page hero: <section class="phero"> with crumbs, eyebrow, h1, lead and optional extra (CTAs / facts). */
    public static function hero(Blueprint $table): void
    {
        $table->string('hero_variant')->nullable()->comment('null = normal, "short" = compact hero');
        $table->string('hero_image', 1024)->nullable();
        $table->string('hero_image_alt')->nullable();
        $table->json('hero_crumbs')->nullable()->comment('[{label, url|null}]');
        $table->text('hero_eyebrow')->nullable();
        $table->text('hero_title')->nullable()->comment('HTML');
        $table->text('hero_subtitle')->nullable()->comment('HTML');
        $table->text('hero_extra')->nullable()->comment('HTML: CTA buttons, facts list');
    }

    public static function seo(Blueprint $table): void
    {
        $table->string('meta_title')->nullable();
        $table->text('meta_description')->nullable();
        $table->string('og_image', 1024)->nullable();
    }

    /** status, sort order, timestamps and soft deletes. */
    public static function publishing(Blueprint $table): void
    {
        $table->string('status', 20)->default('published')->index()->comment('draft | published');
        $table->unsignedInteger('sort_order')->default(0);
        $table->timestamps();
        $table->softDeletes();
    }
}
