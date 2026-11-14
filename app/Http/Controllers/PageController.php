<?php

namespace App\Http\Controllers;

use App\Models\Page;

class PageController extends Controller
{
    /** Single pages (about, faq, terms...) and section index pages, by pages.slug. */
    public function show(string $page)
    {
        return static::render($page);
    }

    public static function render(string $slug)
    {
        $page = (\App\Support\Seo::canPreviewDrafts() ? Page::query() : Page::published())->where('slug', $slug)->firstOrFail();

        return view('content', ['m' => $page]);
    }
}
