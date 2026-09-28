---
title: Upgrade guide
nav_title: Upgrade guide
description: How to stay current on the development branches, and what to change for each breaking change.
section: get-started
category: getting-started
package: all
order: 70
tags: [upgrading]
status: ready
---

Streams packages are installed from development branches until stable tags exist (see [Versions and support](/docs/versions)). Upgrading means pulling the latest commit on your branch and handling any breaking changes listed below. The [Changelog](/docs/changelog) has the full list.

## Updating

```bash
composer update streams/core streams/ui streams/api --with-dependencies
composer update streams/sdk streams/testing
```

Then clear cached config and views, and run your tests:

```bash
php artisan optimize:clear
php artisan test
```

Commit `composer.lock` so your team and CI run the same commits.

## Breaking changes

### OpenSearch adapter moved to Core (August 2026)

The OpenSearch adapter was removed from `streams/ui` and added to `streams/core`, next to a new Elasticsearch adapter.

- Streams using `"source": {"type": "opensearch"}` keep working once Core is updated. Core resolves `opensearch` and `elasticsearch` on its own.
- If you referenced the class directly, change `Streams\Ui\Criteria\Adapter\OpenSearchAdapter` to `Streams\Core\Criteria\Adapter\OpenSearchAdapter`.
- Move connection config from `streams.opensearch.*` to `streams.core.opensearch.*` (publish `config/streams/core.php` or set `OPENSEARCH_HOST`, `OPENSEARCH_USERNAME`, `OPENSEARCH_PASSWORD`, and `OPENSEARCH_SSL_VERIFICATION`).
- Require `opensearch-project/opensearch-php` in your app. Core only suggests it.

### Eager loading uses relation names (July 2026)

`Criteria::with()` now takes the **relation name** and attaches the related entry under that name. The foreign-key attribute stays a scalar.

```php
// Before: the related entry replaced the foreign key
$post = Streams::entries('posts')->with(['author_id'])->first();
$post->author_id; // Entry

// After: ask for the relation name
$post = Streams::entries('posts')->with(['author'])->first();
$post->author;    // Entry
$post->author_id; // 42
```

The relation name is the field handle with a trailing `_id` removed, or the relationship field's `relation` config if set. API clients change `with=author_id` to `with=author` the same way.

### API endpoint builders and explicit interfaces (June 2026)

`streams/api` was rebuilt around interfaces, resources, and invokable endpoints.

| Before | After |
|--------|-------|
| `Streams\Api\Http\Controller\Entries\GetEntries` | `Streams\Api\Endpoints\Entries\ListEntries` |
| `Streams\Api\Http\Controller\Streams\GetStreams` | `Streams\Api\Endpoints\Streams\ListStreams` |
| Other `Streams\Api\Http\Controller\{Entries,Streams}\*` | Same class name under `Streams\Api\Endpoints\{Entries,Streams}\*` |
| `SetUpInterface` middleware, alias `interface`, pushed into the `api` group | `SetUpApiInterface`, alias `api.interface`, applied to API routes only |
| Route names always included the interface ID (`streams.api.api.entries.list`) | The default interface omits it (`streams.api.entries.list`); other interfaces keep it (`streams.api.v1.entries.list`) |
| `enabled` config was informational | `enabled` is enforced by `gate_middleware`; API routes return 404 until `STREAMS_API_ENABLED=true` |

To upgrade:

1. Set `STREAMS_API_ENABLED=true` wherever the API should respond.
2. Register routes with `API::routeCrud()` (or `routeEntries()` / `routeStreams()`) or `API::interface(...)` from a service provider's `boot()` method, not inside a prefixed route group. See [API installation](/docs/api/installation).
3. Move custom controllers that extended the old controllers onto the new endpoint classes. See [Custom endpoints](/docs/api/custom-endpoints).
4. Move authentication into interface middleware or your own gate. See [API authentication](/docs/api/authentication).

## Core 1.x to 2.0

Core 2.0 is a separate branch from the 1.x line (last tag `v1.10.4`) and is what every current Streams package requires. There is no written migration guide from 1.x yet. If you are on 1.x, start from the [Core introduction](/docs/core/introduction) and treat 2.0 as a new install.

## Related

- [Changelog](/docs/changelog)
- [Versions and support](/docs/versions)
