---
id: responses
sort_order: 6
status: published
title: API Responses
description: Format and customize API responses with the ApiResponse class.
---

# API Responses

The Streams API uses the `ApiResponse` class to provide consistent, standardized JSON responses.

## Basic Response

Return data with a standard response structure:

```php
use Streams\Api\ApiResponse;

return ApiResponse::make($data);
```

Response format:

```json
{
    "data": {
        "id": "post-1",
        "title": "Getting Started",
        "status": "published"
    }
}
```

## Response with Collections

When returning multiple items:

```php
$posts = Streams::entries('posts')->get();

return ApiResponse::make($posts);
```

```json
{
    "data": [
        {
            "id": "post-1",
            "title": "First Post"
        },
        {
            "id": "post-2",
            "title": "Second Post"
        }
    ]
}
```

## Status Codes

Set HTTP status codes:

```php
// 201 Created
return ApiResponse::make($entry)->setStatusCode(201);

// 404 Not Found
return ApiResponse::make()->setStatusCode(404);

// 204 No Content
return ApiResponse::make()->setStatusCode(204);

// 422 Unprocessable Entity
return ApiResponse::make()->setStatusCode(422);
```

## Error Responses

Add error messages:

```php
$response = ApiResponse::make();
$response->addError('Post not found');
$response->setStatusCode(404);

return $response;
```

```json
{
    "data": null,
    "errors": [
        "Post not found"
    ]
}
```

Multiple errors:

```php
$response = ApiResponse::make();
$response->addError('Title is required');
$response->addError('Content is required');
$response->setStatusCode(422);

return $response;
```

```json
{
    "data": null,
    "errors": [
        "Title is required",
        "Content is required"
    ]
}
```

## Validation Errors

Handle Laravel validation:

```php
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

public function create(Request $request)
{
    try {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
        ]);
        
        $entry = Streams::entries('posts')->create($validated);
        
        return ApiResponse::make($entry)->setStatusCode(201);
        
    } catch (ValidationException $e) {
        $response = ApiResponse::make();
        
        foreach ($e->errors() as $field => $errors) {
            foreach ($errors as $error) {
                $response->addError($error);
            }
        }
        
        return $response->setStatusCode(422);
    }
}
```

## Response Modifiers

Apply modifiers to transform the response:

```php
return ApiResponse::make($data, [
    'transformer' => MyTransformer::class,
    'include' => 'author,comments',
    'exclude' => 'internal_notes',
]);
```

## Empty Responses

For DELETE operations or other actions without data:

```php
public function delete(string $id)
{
    $entry = Streams::entries('posts')->find($id);
    
    if (!$entry) {
        return ApiResponse::make()
            ->setStatusCode(404)
            ->addError('Post not found');
    }
    
    $entry->delete();
    
    return ApiResponse::make()->setStatusCode(204);
}
```

## Success Messages

Include success messages in responses:

```php
$entry = Streams::entries('posts')->create($data);

return ApiResponse::make([
    'message' => 'Post created successfully',
    'post' => $entry,
])->setStatusCode(201);
```

```json
{
    "data": {
        "message": "Post created successfully",
        "post": {
            "id": "post-1",
            "title": "New Post"
        }
    }
}
```

## Pagination Responses

Paginated data includes metadata automatically:

```php
$posts = Streams::entries('posts')
    ->where('status', 'published')
    ->paginate(15);

return ApiResponse::make($posts);
```

```json
{
    "data": [
        {"id": "post-1", "title": "Post 1"},
        {"id": "post-2", "title": "Post 2"}
    ],
    "links": {
        "first": "/api/streams/posts/entries?page=1",
        "last": "/api/streams/posts/entries?page=5",
        "prev": null,
        "next": "/api/streams/posts/entries?page=2"
    },
    "meta": {
        "current_page": 1,
        "from": 1,
        "last_page": 5,
        "per_page": 15,
        "to": 15,
        "total": 73
    }
}
```

## Custom Response Data

Structure your response data as needed:

```php
return ApiResponse::make([
    'post' => $post,
    'author' => $author,
    'comments_count' => $commentsCount,
    'related_posts' => $relatedPosts,
]);
```

```json
{
    "data": {
        "post": {...},
        "author": {...},
        "comments_count": 42,
        "related_posts": [...]
    }
}
```

## Headers

Add custom headers:

```php
$response = ApiResponse::make($data);

return $response->header('X-Custom-Header', 'value');
```

## Response Examples

### Successful Creation

```php
public function create(Request $request)
{
    $validated = $request->validate([...]);
    
    $entry = Streams::entries('posts')->create($validated);
    
    return ApiResponse::make($entry)
        ->setStatusCode(201)
        ->header('Location', route('streams.api.v1.entries.show', [
            'stream' => 'posts',
            'entry' => $entry->id,
        ]));
}
```

### Not Found

```php
public function show(string $id)
{
    $entry = Streams::entries('posts')->find($id);
    
    if (!$entry) {
        return ApiResponse::make()
            ->setStatusCode(404)
            ->addError('Post not found');
    }
    
    return ApiResponse::make($entry);
}
```

### Update with Validation

```php
public function update(Request $request, string $id)
{
    $entry = Streams::entries('posts')->find($id);
    
    if (!$entry) {
        return ApiResponse::make()
            ->setStatusCode(404)
            ->addError('Post not found');
    }
    
    try {
        $validated = $request->validate([
            'title' => 'sometimes|required|max:255',
            'content' => 'sometimes|required',
        ]);
    } catch (ValidationException $e) {
        $response = ApiResponse::make();
        
        foreach ($e->errors() as $errors) {
            foreach ($errors as $error) {
                $response->addError($error);
            }
        }
        
        return $response->setStatusCode(422);
    }
    
    $entry->fill($validated);
    $entry->save();
    
    return ApiResponse::make($entry);
}
```

### Conflict

```php
public function create(Request $request)
{
    $slug = Str::slug($request->title);
    
    $exists = Streams::entries('posts')
        ->where('slug', $slug)
        ->exists();
    
    if ($exists) {
        return ApiResponse::make()
            ->setStatusCode(409)
            ->addError('A post with this slug already exists');
    }
    
    // Create post...
}
```

### Multiple Errors

```php
$response = ApiResponse::make();

if (!$request->has('title')) {
    $response->addError('Title is required');
}

if (!$request->has('content')) {
    $response->addError('Content is required');
}

if ($response->hasErrors()) {
    return $response->setStatusCode(422);
}

// Process request...
```

## Standard HTTP Status Codes

| Code | Meaning | When to Use |
|------|---------|-------------|
| 200 | OK | Successful GET, PUT, PATCH |
| 201 | Created | Successful POST creating a resource |
| 204 | No Content | Successful DELETE |
| 400 | Bad Request | Invalid request format |
| 401 | Unauthorized | Authentication required |
| 403 | Forbidden | Authenticated but not authorized |
| 404 | Not Found | Resource doesn't exist |
| 409 | Conflict | Resource already exists |
| 422 | Unprocessable Entity | Validation failed |
| 429 | Too Many Requests | Rate limit exceeded |
| 500 | Internal Server Error | Server error |

## Best Practices

1. **Always use ApiResponse** - Ensures consistent format
2. **Set appropriate status codes** - Helps clients handle responses
3. **Provide clear error messages** - Makes debugging easier
4. **Include relevant data** - Don't return more than needed
5. **Handle validation properly** - Return all errors at once
6. **Use pagination for lists** - Improves performance

## Next Steps

- [Middleware](middleware) - Add authentication and rate limiting
- [Testing](testing) - Test your API responses
- [OpenAPI Documentation](openapi) - Auto-generate API docs
