<?php

namespace App\Http\Middleware;

use App\Models\Redirect;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

/** Global middleware: GET/HEAD requests whose path is in the redirects table are redirected (query string kept). */
class HandleRedirects
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->isMethod('GET') && ! $request->isMethod('HEAD')) {
            return $next($request);
        }

        $path = Redirect::normalize($request->getPathInfo());
        $target = rescue(fn () => Redirect::map()[$path] ?? null, null, false); // no database: serve the page normally
        if ($target === null) {
            return $next($request);
        }

        [$to, $status] = $target;
        // Count the hit without model events (they would clear the cached map).
        rescue(fn () => DB::table('redirects')->where('from_path', $path)
            ->update(['hits' => DB::raw('hits + 1'), 'last_hit_at' => now()]), null, false);

        $url = preg_match('#^https?://#i', $to) ? $to : url($to);
        $query = $request->getQueryString();

        return redirect()->to($query ? $url.(str_contains($url, '?') ? '&' : '?').$query : $url, $status);
    }
}
