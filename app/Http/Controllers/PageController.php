<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

/**
 * Phase 1: serves the converted static pages. Content is still hard-coded in the
 * Blade views; Phase 2 will load it from the database.
 */
class PageController extends Controller
{
    public function home()
    {
        return view('home');
    }

    public function page(string $view)
    {
        return view($view);
    }

    /** /{section}/{slug} -> resources/views/{section}/pages/{slug}.blade.php */
    public function show(Request $request)
    {
        $route = $request->route();
        $view = "{$route->parameter('section')}.pages.{$route->parameter('slug')}";
        abort_unless(View::exists($view), 404);

        return view($view);
    }
}
