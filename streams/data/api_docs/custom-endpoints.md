---
title: Custom endpoints
description: 'Add endpoints to an interface, write ApiEndpoint classes, and group them in resources.'
sort_order: 13
status: ready
---

Custom endpoints live on an [interface](/docs/api/custom-interfaces), so they share its path, middleware, and gate.

## Quick endpoints

String keys passed to `endpoints()` are registered as GET routes inside the interface group:

```php
use Streams\Api\ApiInterface;
use Streams\Api\Support\Facades\API;

API::interface(
    ApiInterface::make('api')
        ->path('api')
        ->endpoints([
            'health' => fn () => response()->json(['status' => 'ok']),
            'stats' => \App\Http\Controllers\StatsController::class,
        ])
);
// GET /api/health, GET /api/stats
```

## Endpoint classes

Extend `Streams\Api\Builders\Endpoints\ApiEndpoint`, implement `__invoke()`, and return `ApiResponse::make()`. The static `route()` method returns an `EndpointRouter` you can pass to `endpoints()` or a resource:

```php
namespace App\Api;

use Illuminate\Http\JsonResponse;
use Streams\Api\ApiResponse;
use Streams\Api\Builders\Endpoints\ApiEndpoint;

class FeaturedPosts extends ApiEndpoint
{
    public function __invoke(): JsonResponse
    {
        $response = new ApiResponse('posts');
        $criteria = $response->stream->entries();

        $this->fire('apply', compact('criteria'));

        return $response->make(
            $criteria->where('featured', true)->limit(10)->get()
        );
    }
}
```

```php
->endpoints([
    FeaturedPosts::route('posts/featured', 'get', 'posts.featured'),
])
```

`route(string $path, string|array $methods = 'get', ?string $routeName = null, array $where = [])` registers the route when the interface mounts. Endpoint instances also accept `routeMiddleware()` and `withoutRouteMiddleware()`.

Firing `apply` keeps your endpoint compatible with listeners such as [tenant scoping](/docs/api/tenancy).

## Resources

Group related endpoints in an `ApiResource`. Endpoint names are appended to the resource's route name prefix:

```php
namespace App\Api;

use Streams\Api\ApiResource;

class PostsResource extends ApiResource
{
    protected static ?string $slug = 'posts';

    protected static string|array $middleware = ['auth:sanctum'];

    public static function getEndpoints(): array
    {
        return [
            'featured' => FeaturedPosts::route('featured', 'get'),
        ];
    }
}
```

With `usesRoutePrefix()` returning `true` (the default), routes are prefixed with the slug: `GET /api/posts/featured`, named `streams.api.posts.featured`. `EntriesResource` and `StreamsResource` return `false` because their paths already start with `streams/`.

## Override a built-in endpoint

Subclass a built-in endpoint and point a resource subclass at it:

```php
use Streams\Api\Endpoints\Entries\CreateEntry;
use Streams\Api\Resources\EntriesResource;

class AuditedCreateEntry extends CreateEntry
{
    public function __invoke(string $stream): \Illuminate\Http\JsonResponse
    {
        $response = parent::__invoke($stream);

        \Illuminate\Support\Facades\Log::info("API created an entry in {$stream}");

        return $response;
    }
}

class AppEntriesResource extends EntriesResource
{
    public static function getEndpoints(): array
    {
        return [
            ...parent::getEndpoints(),
            'create' => AuditedCreateEntry::route('streams/{stream}/entries', 'post'),
        ];
    }
}
```

## Related

- [Custom interfaces](/docs/api/custom-interfaces)
- [Responses](/docs/api/responses)
- [Routes](/docs/api/routes)
