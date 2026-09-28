<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Permanently redirects docs URLs that moved or were merged (config/docs.php).
 *
 * Runs as global middleware, before routing, because the docs stream routes
 * (docs/{id}, docs/sdk/{id}) are registered before routes/web.php and would
 * otherwise answer the old URL with a 404.
 */
class RedirectMovedDocs
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->isMethod('GET') && ! $request->isMethod('HEAD')) {
            return $next($request);
        }

        $path = trim($request->path(), '/');
        $markdown = str_ends_with($path, '.md');
        $redirects = (array) config('docs.redirects', []);
        $key = $markdown ? substr($path, 0, -3) : $path;

        if (! isset($redirects[$key])) {
            return $next($request);
        }

        [$target, $fragment] = array_pad(explode('#', $redirects[$key], 2), 2, null);

        if ($markdown) {
            // Raw markdown has no anchors: point at the whole page.
            [$target, $fragment] = [$target.'.md', null];
        }

        $query = $request->getQueryString();

        return redirect()->to($target.($query ? '?'.$query : '').($fragment ? '#'.$fragment : ''), 301);
    }
}
