<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        // Public docs MCP server (routes/ai.php). Keyed by client IP, which
        // TrustProxies resolves from X-Forwarded-For behind Cloudflare.
        RateLimiter::for('mcp', function (Request $request) {
            return Limit::perMinute(60)
                ->by('mcp|'.$request->ip())
                ->response(function (Request $request, array $headers) {
                    return response()->json([
                        'jsonrpc' => '2.0',
                        'id' => $request->json('id'),
                        'error' => [
                            'code' => -32000,
                            'message' => 'Rate limit exceeded. Retry after '.($headers['Retry-After'] ?? 60).' seconds.',
                        ],
                    ], 429, $headers);
                });
        });
    }
}
