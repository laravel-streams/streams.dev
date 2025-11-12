---
id: builder
sort_order: 3
status: drafting
title: Builder::make(...) API Guide
description: Document the Builder contract and examples for using Builder::make(...) to create API endpoints.
---

## Table of Contents

- Purpose and contract
- Usage examples
  - Basic: Builder::make('posts')->paginate()
  - Filtering and sorting
  - Includes / relations
  - Custom transformers
- Chainable methods
- Integration with policies and middleware
- Migration: adapter for legacy controllers

## Purpose

This page will describe the Builder contract (Builder::make(...)) and how to use it to build consistent API endpoints. Examples will show how to preserve the existing JSON envelope while enabling a clearer API surface.

## Builder contract (detailed)

The Builder is a lightweight, chainable API for composing Stream queries and producing consistent JSON responses. The contract below is intentional: keep method names small, chainable, and expressive so both UI and API code can use the same surface.

Core methods (static factory + chains)

- Builder::make(string|StreamDefinition $stream)
  - Create a Builder for the given stream identifier or stream definition object.
- ->filter(array|string $filters)
  - Apply filtering rules. Accepts a structured array (recommended) or a query string.
- ->sort(string|array $spec)
  - Sort by one or more fields. Accepts "field" or ["field" => "desc"].
- ->with(array|string $relations)
  - Eager load related resources.
- ->select(array|string $fields)
  - Select a subset of fields (sparse fieldsets).
- ->page(int $page, int $perPage = null)
  - Set pagination parameters. If omitted, use default pagination from config.
- ->limit(int $n)
  - Limit number of results (for non-paginated endpoints).
- ->transformWith(callable|string $transformer)
  - Optional: attach a transformer (class or closure) to map entries to response shape.
- ->policy(string|callable $ability)
  - Optional: restrict results by policy/ability checks.

Execution / response methods

- ->get(): Collection|array
  - Execute and return raw collection.
- ->first(): Model|null
  - Return a single entry.
- ->paginate(int $perPage = null): Paginator
  - Execute and return a paginator (includes meta and links).
- ->respond(array $options = []): JsonResponse
  - Convenience: run the query and return a Laravel JsonResponse using the standard Streams JSON envelope (data/meta/links). Options can control envelope keys or include additional meta.

Errors and behavior

- Validation errors should throw an Illuminate\\Validation\\ValidationException and result in a 422 response with the Streams error envelope.
- Authorization failures should throw AuthorizationException (403) or AuthenticationException (401) per Laravel conventions.
- Builder must not change the shape of the existing JSON envelope; `->respond()` should produce { data, meta, links } unless versioned.

## Example usage

Basic collection endpoint (controller):

```php
use Streams\\Api\\Builder;

public function index(Request $request)
{
    return Builder::make('posts')
        ->filter($request->query('filter', []))
        ->sort($request->query('sort'))
        ->with($request->query('include'))
        ->paginate((int)$request->query('per_page', 15))
        ->respond();
}
```

Single-entry endpoint:

```php
public function show($id)
{
    return Builder::make('posts')
        ->policy('view')
        ->with(['author', 'comments'])
        ->firstOrFail($id)
        ->respond();
}
```

Custom transformer example (map fields):

```php
return Builder::make('posts')
    ->filter($filters)
    ->transformWith(PostResource::class)
    ->paginate()
    ->respond();
```

## Migration snippet (legacy controller -> Builder)

Old style (manual Eloquent + custom envelope):

```php
$query = Post::with('author')->where('status', 'published');
$results = $query->paginate(15);
return response()->json([
    'data' => $results->items(),
    'meta' => [ 'pagination' => /* ... */ ]
]);
```

New style (Builder) — same response envelope, less plumbing:

```php
return Builder::make('posts')
    ->filter(['status' => 'published'])
    ->with('author')
    ->paginate(15)
    ->respond();
```

## Notes for implementers

- Keep query parsing centralized (filters, sort, include parsing) to avoid inconsistent behaviour across endpoints.
- Ensure the `respond()` helper uses config for default keys (e.g., data/meta/links) so we can version the contract later if needed.
- Provide adapter helpers to wrap legacy controllers returning raw Eloquent collections.

<!-- Expand with more detailed signatures, param shapes, and code examples as you complete docs -->
