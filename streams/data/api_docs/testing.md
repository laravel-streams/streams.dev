---
title: 'API: Testing'
nav_title: Testing
description: Test your API routes with Laravel HTTP tests, and the status codes to expect.
section: packages
package: api
order: 190
tags: [api, testing]
status: ready
---

Test your app's API routes with ordinary Laravel HTTP tests. `streams/api` has no test base class for apps: its `Streams\Api\Tests\ApiTestCase` is in the package's `autoload-dev`, so it isn't autoloaded when you install the package, and it extends the `streams/testing` harness, which is Laravel 10 only for now.

## HTTP test

Routes exist once your app registers them, for example with `API::routeCrud()` in a service provider's `boot()` method (see [Installation](/docs/api/installation#register-routes)). Your normal `Tests\TestCase` boots the app, so the routes are there:

```php
namespace Tests\Feature;

use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class FilmsApiTest extends TestCase
{
    public function test_it_lists_films(): void
    {
        $this->getJson(URL::route('streams.api.entries.list', ['stream' => 'films']))
            ->assertOk()
            ->assertJsonStructure(['data', 'errors', 'links', 'meta']);
    }

    public function test_it_creates_a_film(): void
    {
        $this->postJson(URL::route('streams.api.entries.create', ['stream' => 'films']), [
            'title' => 'The Last Jedi',
        ])->assertCreated();
    }
}
```

If you register the routes conditionally (for example only when an env flag is set), set that flag in `phpunit.xml` or call `API::routeCrud()` in your test's `setUp()`.

Tests that create or delete entries write to the stream's source. Point the source at a temporary path in tests, or restore the data afterwards (see [Testing configuration](/docs/testing/configuration)).

## Status expectations

| Operation | Expected status |
|-----------|-----------------|
| List/show | 200 |
| Create | 201 |
| Update (PUT/PATCH) an existing entry | 200 |
| Update (PUT/PATCH) an entry that doesn't exist | 201 (it is created) |
| Validation failure | 409 |
| Not found (show/delete) | 404 |
| Delete success | 204 |

## Related

- [Errors](/docs/api/errors)
- [Hub: Testing](/docs/testing)
