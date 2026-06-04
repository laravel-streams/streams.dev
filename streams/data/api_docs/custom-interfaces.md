---
id: custom-interfaces
sort_order: 4
status: published
title: Custom Interfaces
description: Create multiple API interfaces with different configurations, prefixes, and middleware.
---

# Custom Interfaces

API Interfaces allow you to create multiple API configurations with different prefixes, middleware, and endpoints. This is perfect for versioning your API or creating specialized endpoints.

## Creating an Interface

Use the `ApiInterface` class to define custom API interfaces:

```php
use Streams\Api\ApiInterface;
use Streams\Api\Support\Facades\API;

$api = new ApiInterface('v1');
$api->path('api/v1');
$api->middleware(['auth:sanctum', 'throttle:60,1']);

API::interface($api);
```

Then register routes in your service provider:

```php
use Streams\Api\Support\Facades\API;

public function boot()
{
    API::routeEntries();
    API::routeStreams();
}
```

## Interface Configuration

### Setting the ID

The interface ID is used for route naming:

```php
$api = new ApiInterface('v1');
// Routes will be named: streams.api.v1.entries.list, etc.
```

### Setting the Path

The path is the URL prefix:

```php
$api->path('api/v1');
// Endpoints: /api/v1/streams, /api/v1/streams/{stream}/entries
```

If not set, the ID is used as the path.

### Adding Middleware

Apply middleware to all routes in the interface:

```php
$api->middleware(['auth:sanctum']);
$api->middleware(['throttle:60,1']); // Adds to existing middleware
```

## Using Resources

Resources allow you to organize related endpoints:

```php
use Streams\Api\ApiResource;
use Streams\Api\ApiInterface;

class PostsResource extends ApiResource
{
    protected static ?string $slug = 'posts';
    
    protected static ?string $routeMiddleware = 'auth:sanctum';
    
    public static function routes(ApiInterface $interface): void
    {
        Route::get('featured', [static::class, 'featured'])
            ->name('featured');
            
        Route::get('{post}/related', [static::class, 'related'])
            ->name('related');
    }
    
    public function featured()
    {
        $posts = Streams::entries('posts')
            ->where('featured', true)
            ->get();
            
        return ApiResponse::make($posts);
    }
    
    public function related(string $post)
    {
        // Implementation
    }
}
```

Register the resource:

```php
$api->resources([PostsResource::class]);
```

This creates routes:
- `GET /api/v1/posts/featured`
- `GET /api/v1/posts/{post}/related`

## Custom Endpoints

Add custom endpoints directly to an interface:

```php
$api->endpoints([
    'health' => function () {
        return response()->json(['status' => 'ok']);
    },
    'stats' => StatsController::class,
]);
```

## Custom Routes Closure

For complete control, use a routes closure:

```php
use Illuminate\Support\Facades\Route;

$api->routes(function (ApiInterface $interface) {
    Route::get('custom', function () {
        return response()->json(['message' => 'Custom endpoint']);
    });
    
    Route::post('webhook', WebhookController::class);
    
    Route::prefix('admin')->middleware('admin')->group(function () {
        Route::get('dashboard', [AdminController::class, 'dashboard']);
    });
});
```

## Complete Example: Versioned API

```php
use Streams\Api\ApiInterface;
use Streams\Api\Support\Facades\API;
use Illuminate\Support\ServiceProvider;

class ApiServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Version 1 API - Public
        $v1 = new ApiInterface('v1');
        $v1->path('api/v1');
        $v1->middleware(['api', 'throttle:60,1']);
        
        API::interface($v1);
        
        // Version 2 API - Authenticated
        $v2 = new ApiInterface('v2');
        $v2->path('api/v2');
        $v2->middleware(['api', 'auth:sanctum', 'throttle:100,1']);
        $v2->resources([
            PostsResource::class,
            UsersResource::class,
        ]);
        
        API::interface($v2);
        
        // Admin API
        $admin = new ApiInterface('admin');
        $admin->path('api/admin');
        $admin->middleware(['api', 'auth:sanctum', 'admin']);
        $admin->routes(function ($interface) {
            Route::get('stats', [AdminController::class, 'stats']);
            Route::post('cache/clear', [AdminController::class, 'clearCache']);
        });
        
        API::interface($admin);
        
        // Register default routes for all interfaces
        API::routeEntries();
        API::routeStreams();
    }
}
```

This creates three separate APIs:
- `/api/v1/*` - Public with rate limiting
- `/api/v2/*` - Authenticated with higher rate limit
- `/api/admin/*` - Admin-only endpoints

## Fluent Interface

All configuration methods return the interface for chaining:

```php
$api = (new ApiInterface('api'))
    ->path('api/v1')
    ->middleware(['auth:sanctum'])
    ->resources([PostsResource::class])
    ->endpoints([
        'health' => HealthController::class,
    ]);

API::interface($api);
```

## Multiple Interfaces

You can register as many interfaces as needed:

```php
API::interface($publicApi);
API::interface($privateApi);
API::interface($adminApi);
API::interface($webhookApi);
```

Each interface is independent with its own:
- URL prefix
- Middleware stack
- Custom routes
- Resources

## Next Steps

- [Custom Endpoints](custom-endpoints) - Deep dive into custom route creation
- [Resources](resources) - Learn more about API resources
- [Middleware](middleware) - Apply authentication and authorization
