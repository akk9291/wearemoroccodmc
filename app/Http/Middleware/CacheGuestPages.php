<?php

namespace App\Http\Middleware;

use App\Support\ContentCache;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

/**
 * Full-page cache for guests (web group, after the session starts).
 * Only GET/HEAD HTML pages with status 200, no query string, no signed-in user and no flash/validation data are cached.
 * The visitor's own CSRF token is put back into every cached page, so forms keep working.
 * Stale as soon as any content changes (ContentCache version); PAGE_CACHE=false turns it off.
 */
class CacheGuestPages
{
    public const TOKEN = '__WAM_CSRF_TOKEN__';

    public const TTL = 86400;

    public function handle(Request $request, Closure $next): Response
    {
        if (! $this->cacheable($request)) {
            return $next($request);
        }

        $key = ContentCache::key('page.'.sha1($request->getSchemeAndHttpHost().$request->getPathInfo()));
        $token = $request->session()->token();

        if (is_string($html = Cache::get($key))) {
            return response(str_replace(self::TOKEN, $token, $html), 200, [
                'Content-Type' => 'text/html; charset=UTF-8',
                'X-Page-Cache' => 'HIT',
            ]);
        }

        $response = $next($request);

        if ($response->getStatusCode() === 200 && $this->isHtml($response) && ! $this->sessionHasMessages($request)
            && auth()->guest()) {
            Cache::put($key, str_replace($token, self::TOKEN, (string) $response->getContent()), self::TTL);
            $response->headers->set('X-Page-Cache', 'MISS');
        }

        return $response;
    }

    private function cacheable(Request $request): bool
    {
        return config('cache.page_cache', true)
            && ($request->isMethod('GET') || $request->isMethod('HEAD'))
            && $request->getQueryString() === null
            && $request->hasSession()
            && auth()->guest()
            && ! $this->sessionHasMessages($request);
    }

    /** Form results and errors are shown from the session: such a page belongs to one visitor only. */
    private function sessionHasMessages(Request $request): bool
    {
        $session = $request->session();

        return $session->has('errors') || $session->has('_old_input') || $session->has('form_sent')
            || ! empty($session->get('_flash.new')) || ! empty($session->get('_flash.old'));
    }

    private function isHtml(Response $response): bool
    {
        return str_starts_with((string) $response->headers->get('Content-Type'), 'text/html');
    }
}
