---
title: Testing
description: 'ApiTestCase, route setup, and status expectations.'
sort_order: 16
status: ready
---

Use `Streams\Api\Tests\ApiTestCase` for API integration tests.

## Base test case

```php
namespace Tests\Api;

use Streams\Api\Tests\ApiTestCase;
use Illuminate\Support\Facades\URL;

class FilmsApiTest extends ApiTestCase
{
    public function test_it_lists_films(): void
    {
        $response = $this->get(URL::route('streams.api.entries.list', [
            'stream' => 'films',
        ]));

        $response->assertStatus(200)
            ->assertJsonStructure(['data', 'errors', 'links', 'meta']);
    }
}
```

## Automatic setup

`ApiTestCase::setUp()` registers `ApiServiceProvider` and calls:

- `API::routeEntries()`
- `API::routeStreams()`

## Status expectations

| Operation | Expected status |
|-----------|-----------------|
| List/show | 200 |
| Create | 201 |
| Validation failure | 409 |
| Not found | 404 |
| Delete success | 204 |

## Related

- [Errors](/docs/api/errors)
- [Hub: Testing](/docs/testing)
