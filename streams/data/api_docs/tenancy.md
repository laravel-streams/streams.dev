---
title: Tenancy
description: 'Resolve a tenant once per request with API::tenant() or ApiInterface::tenant(), then scope criteria with endpoint callbacks.'
sort_order: 15
status: ready
---

The API can resolve **one tenant per request** and hand it to your code. It does not decide what a tenant is or filter data for you: you supply a resolver, then use the tenant to scope queries.

For site-level multi-tenancy (different config, data paths, or streams per domain), see Core [Applications](/docs/core/applications) and the [Tenancy guide](/docs/tenancy).

## Resolve a tenant

Register a global resolver in a service provider:

```php
use Streams\Api\Support\Facades\API;

API::tenant(fn () => request()->user()?->organization_id);
```

Or set one per interface. An interface resolver takes precedence over the global one for routes on that interface:

```php
use Streams\Api\ApiInterface;

API::interface(
    ApiInterface::make('partners')
        ->path('api/partners')
        ->middleware(['auth:sanctum'])
        ->tenant(fn () => request()->header('X-Partner'))
);
```

Read it anywhere during the request:

```php
$tenant = API::getTenant();
```

`API::getTenant()` calls the resolver once and binds the result in the container as `streams.api.tenant`, so later calls in the same request return the cached value. If no resolver is registered it returns `null`.

## Scope queries

`ListEntries` and `ShowEntry` fire an `apply` callback with the criteria before they run the query (`ListEntries` also fires `applied` after filters). Add a listener to scope every request:

```php
use Streams\Api\Endpoints\Entries\ListEntries;
use Streams\Api\Endpoints\Entries\ShowEntry;
use Streams\Api\Support\Facades\API;
use Streams\Core\Criteria\Criteria;

$scope = function (Criteria $criteria) {
    if ($tenant = API::getTenant()) {
        $criteria->where('organization_id', $tenant);
    }
};

ListEntries::addCallbackListener('apply', $scope);
ShowEntry::addCallbackListener('apply', $scope);
```

The callback receives the criteria as `$criteria`, because listeners are called through the container with named parameters.

## What is not scoped

Only the list and show endpoints fire `apply`. Create, update, patch, delete, and query, plus all stream endpoints, do **not**. To enforce tenancy on writes:

- Put tenant checks in your [gate middleware](/docs/api/authentication) or interface middleware, or
- Replace the endpoints with your own subclasses (see [Custom endpoints](/docs/api/custom-endpoints)), or
- Keep tenants in separate data sources with Core [Applications](/docs/core/applications).

## Related

- [Authentication](/docs/api/authentication)
- [Custom interfaces](/docs/api/custom-interfaces)
- [Tenancy guide](/docs/tenancy)
