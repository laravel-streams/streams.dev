---
id: custom-endpoints
sort_order: 5
status: published
title: Custom Endpoints
description: Add custom API endpoints with full control over routing and logic.
---

# Custom Endpoints

While the Streams API provides automatic CRUD endpoints, you'll often need custom logic. Here's how to add your own endpoints.

## Quick Custom Endpoints

The simplest way is using the `endpoints()` method:

```php
use Streams\Api\ApiInterface;

$api = new ApiInterface('v1');
$api->endpoints([
    'health' => function () {
        return response()->json([
            'status' => 'healthy',
            'timestamp' => now(),
        ]);
    },
]);
```

Access at: `GET /api/v1/health`

## Using Controllers

For more complex logic, use controllers:

```php
$api->endpoints([
    'stats' => StatsController::class,
    'export' => [ExportController::class, 'export'],
]);
```

### Stats Controller Example

```php
namespace App\Http\Controllers\Api;

use Streams\Api\ApiResponse;
use Streams\Core\Support\Facades\Streams;

class StatsController
{
    public function __invoke()
    {
        $stats = [
            'total_posts' => Streams::entries('posts')->count(),
            'published_posts' => Streams::entries('posts')
                ->where('status', 'published')
                ->count(),
            'total_users' => Streams::entries('users')->count(),
            'disk_usage' => $this->getDiskUsage(),
        ];
        
        return ApiResponse::make($stats);
    }
    
    private function getDiskUsage()
    {
        return [
            'used' => disk_free_space('/'),
            'total' => disk_total_space('/'),
        ];
    }
}
```

## Routes Closure

For complete routing control, use the `routes()` method:

```php
use Illuminate\Support\Facades\Route;

$api->routes(function ($interface) {
    // Simple GET route
    Route::get('health', function () {
        return response()->json(['status' => 'ok']);
    })->name('health');
    
    // POST route with controller
    Route::post('export', [ExportController::class, 'export'])
        ->name('export');
    
    // Route with parameters
    Route::get('posts/{post}/related', [PostController::class, 'related'])
        ->name('posts.related');
    
    // Grouped routes
    Route::prefix('admin')->middleware('admin')->group(function () {
        Route::get('dashboard', [AdminController::class, 'dashboard']);
        Route::post('cache/clear', [AdminController::class, 'clearCache']);
    });
});
```

## API Resources

For organized, reusable endpoint groups, create API resources:

```php
namespace App\Api\Resources;

use Streams\Api\ApiResource;
use Streams\Api\ApiInterface;
use Streams\Api\ApiResponse;
use Illuminate\Support\Facades\Route;

class PostsResource extends ApiResource
{
    protected static ?string $slug = 'posts';
    
    protected static ?string $routeMiddleware = 'auth:sanctum';
    
    public static function routes(ApiInterface $interface): void
    {
        Route::get('featured', [static::class, 'featured'])
            ->name('featured');
            
        Route::get('trending', [static::class, 'trending'])
            ->name('trending');
            
        Route::get('{post}/related', [static::class, 'related'])
            ->name('related');
            
        Route::post('{post}/publish', [static::class, 'publish'])
            ->name('publish');
    }
    
    public function featured()
    {
        $posts = Streams::entries('posts')
            ->where('featured', true)
            ->where('status', 'published')
            ->orderBy('featured_at', 'desc')
            ->limit(10)
            ->get();
            
        return ApiResponse::make($posts);
    }
    
    public function trending()
    {
        $posts = Streams::entries('posts')
            ->where('status', 'published')
            ->where('created_at', '>=', now()->subDays(7))
            ->orderBy('views', 'desc')
            ->limit(10)
            ->get();
            
        return ApiResponse::make($posts);
    }
    
    public function related(string $post)
    {
        $entry = Streams::entries('posts')->find($post);
        
        if (!$entry) {
            return ApiResponse::make()
                ->setStatusCode(404)
                ->addError('Post not found');
        }
        
        $related = Streams::entries('posts')
            ->where('category', $entry->category)
            ->where('id', '!=', $post)
            ->where('status', 'published')
            ->limit(5)
            ->get();
            
        return ApiResponse::make($related);
    }
    
    public function publish(string $post)
    {
        $entry = Streams::entries('posts')->find($post);
        
        if (!$entry) {
            return ApiResponse::make()
                ->setStatusCode(404)
                ->addError('Post not found');
        }
        
        $entry->status = 'published';
        $entry->published_at = now();
        $entry->save();
        
        return ApiResponse::make($entry)
            ->setStatusCode(200);
    }
}
```

Register the resource:

```php
$api->resources([PostsResource::class]);
```

Routes created:
- `GET /api/v1/posts/featured`
- `GET /api/v1/posts/trending`
- `GET /api/v1/posts/{post}/related`
- `POST /api/v1/posts/{post}/publish`

## Working with Streams Data

### Querying Entries

```php
use Streams\Core\Support\Facades\Streams;

// Get all entries
$entries = Streams::entries('posts')->get();

// With filtering
$published = Streams::entries('posts')
    ->where('status', 'published')
    ->orderBy('created_at', 'desc')
    ->get();

// Single entry
$entry = Streams::entries('posts')->find($id);

// Create entry
$entry = Streams::entries('posts')->create([
    'title' => 'New Post',
    'content' => 'Content here...',
]);

// Update entry
$entry->title = 'Updated Title';
$entry->save();

// Delete entry
$entry->delete();
```

### Handling Relationships

```php
public function getPostWithComments(string $post)
{
    $entry = Streams::entries('posts')->find($post);
    
    if (!$entry) {
        return ApiResponse::make()
            ->setStatusCode(404)
            ->addError('Post not found');
    }
    
    // Get related comments
    $comments = Streams::entries('comments')
        ->where('post_id', $post)
        ->where('approved', true)
        ->orderBy('created_at', 'desc')
        ->get();
    
    return ApiResponse::make([
        'post' => $entry,
        'comments' => $comments,
    ]);
}
```

## Request Validation

Use Laravel's validation:

```php
use Illuminate\Http\Request;

public function create(Request $request)
{
    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'content' => 'required|string',
        'status' => 'in:draft,published',
        'category' => 'required|exists:categories,id',
    ]);
    
    $entry = Streams::entries('posts')->create($validated);
    
    return ApiResponse::make($entry)
        ->setStatusCode(201);
}
```

With custom error handling:

```php
try {
    $validated = $request->validate([...]);
} catch (\Illuminate\Validation\ValidationException $e) {
    $response = ApiResponse::make();
    
    foreach ($e->errors() as $field => $errors) {
        foreach ($errors as $error) {
            $response->addError($error);
        }
    }
    
    return $response->setStatusCode(422);
}
```

## Route Naming

Routes are automatically named with the pattern:
```
streams.api.{interface_id}.{resource_slug}.{route_name}
```

Examples:
- `streams.api.v1.posts.featured`
- `streams.api.v1.posts.related`
- `streams.api.admin.stats`

Generate URLs:

```php
route('streams.api.v1.posts.featured')
// /api/v1/posts/featured

route('streams.api.v1.posts.related', ['post' => 'my-post'])
// /api/v1/posts/my-post/related
```

## Middleware on Custom Endpoints

### Interface-Level Middleware

Applies to all routes:

```php
$api->middleware(['auth:sanctum', 'throttle:60,1']);
```

### Resource-Level Middleware

Applies to all resource routes:

```php
protected static ?string $routeMiddleware = 'auth:sanctum';

// Or multiple
protected static ?string $routeMiddleware = ['auth:sanctum', 'verified'];
```

### Route-Level Middleware

Applies to specific routes:

```php
Route::post('admin/clear-cache', [AdminController::class, 'clearCache'])
    ->middleware(['auth:sanctum', 'admin']);
```

## Complete Example: Blog API

```php
use Streams\Api\ApiInterface;
use App\Api\Resources\PostsResource;
use App\Api\Resources\CommentsResource;

$blog = new ApiInterface('blog');
$blog->path('api/blog');
$blog->middleware(['api', 'throttle:100,1']);

// Custom endpoints
$blog->endpoints([
    'search' => [SearchController::class, 'search'],
    'tags/popular' => [TagController::class, 'popular'],
]);

// Resources with organized endpoints
$blog->resources([
    PostsResource::class,
    CommentsResource::class,
]);

// Custom routes
$blog->routes(function ($interface) {
    Route::get('sitemap', [SitemapController::class, 'index']);
    Route::post('newsletter/subscribe', [NewsletterController::class, 'subscribe']);
});

API::interface($blog);
```

## Next Steps

- [Responses](responses) - Format API responses
- [Middleware](middleware) - Add authentication and authorization
- [Testing](testing) - Test your custom endpoints
