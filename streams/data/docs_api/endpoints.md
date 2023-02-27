---
title: Endpoints
category: basics
sort: 0
enabled: true
---

## Resources

The API package is a universal RESTful API and has some resources available right out of the box.

### Entries

Entries represent the data within your configured [Streams](streams). Use the Entries API to interact with streams data.

- [Available Methods](/docs/api/entries)


### Streams

Entries represent the data within your configured [Streams](#streams). Use the Entries API to interact with streams data.

- [Available Methods](/docs/api/streams)


## Custom Endpoints

In general, routing endpoints for the API is very similar to routing anything else in Laravel.

If you are using the [ApiCache](caching) middleware you will want to make sure and include the `stream` hint in your routes action.

```php
// routes/api.php
Route::streams('examples/{id}/do-something', [
    'stream' => 'examples',
    'uses' => DoSomething::class
]);
```

### Defining API Routes

You can define API in a number of different 

#### API Routes File

The `app/Providers/RouteServiceProvider.php` file typically uses the `api` middleware group when loading the `routes/api.php` file. By default this is compatible and routes defined there will be properly prefixed and grouped.

#### Using the Router

You may also route API endpoints using the router directly.

```php
Route::prefix('api')
    ->middleware('api')
    ->group(function () {
        // Define your routes.
    });
```

When routing API endpoints from a package, the API prefix and middleware group is potentially unknown. You may use the API configuration to determine the correct values:

```php
Route::prefix(Config::get('streams.api.prefix', 'api'))
    ->middleware(Config::get('streams.api.middleware', 'api'))
    ->group(function () {
        // Define your routes.
    });
```

### API Responses

You may use the `ApiResponse` utility to return standardized JSON responses.

Responses will automatically include built-in links and meta and will default to a `200` status code.

```php
// app\Controller\Api\DoSomething;
use Streams\Api\ApiResponse;

public function __invoke($id)
{
    $response = new ApiResponse('examples');

    // Your endpoint logic...

    return $response->make();
}
```

#### Available Methods

Below is a list of methods you can use to configure the response:


```php
$response->setStatus(int $status): self

$response->addHeader(string $name, mixed $value): self
$response->removeHeader(string $name): self

$response->addError(array $error): self

$response->addLink(string $name, mixed $value): self
$response->removeLink(string $name): self

$response->addMeta(string $name, mixed $value): self
$response->removeMeta(string $name): self

$response->setData(mixed $data): self

$response->make(mixed $data = null, int $status = null, array $headers = []): JsonResponse
```
