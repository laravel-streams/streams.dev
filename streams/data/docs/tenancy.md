---
title: Tenancy
nav_title: Tenancy
description: 'Three separate mechanisms: Core applications matched by URL, an API tenant resolver you scope yourself, and a UI panel tenant that does not scope routes yet.'
section: guides
category: advanced
package: all
order: 70
tags: [tenancy]
status: ready
---

Streams does not ship one tenancy product. Three packages each hold a piece, and none of them filters data unless you do.

| Need | Use |
|------|-----|
| Different config, streams, or locale per host | Core [applications](/docs/core/applications) |
| One tenant value per API request, then scope queries yourself | [API tenancy](/docs/api/tenancy) |
| A tenant value available to a panel | UI `Panel::tenant()` — stored only, see below |

## Applications

An application is an entry on the stream `config('streams.core.applications_id')` (default `applications`). Core loads that stream at boot, picks one entry by matching `match` patterns against the full request URL (`Str::is`), and activates it. If nothing matches, it uses the first entry with an empty `match`. If the stream is empty, it activates a synthetic `default` application.

`StreamsServiceProvider::bootApplication()` then passes these attributes to `Integrator::integrate()` when they are non-empty:

- `locale` — `App::setLocale()`
- `config` — merged with `Config::set()` after dotting the array
- `aliases`, `bindings`, `singletons` — container registrations
- `streams` — each value is a file path passed to `Streams::load()`, or an array passed to `Streams::register()`

The shipped `applications` stream schema only declares `handle`, `match`, and `config`. The other keys are still read off the entry when the JSON file contains them.

`Integrator` can also merge `routes`, `assets`, `commands`, `listeners`, `policies`, `middleware`, `providers`, `schedules`, and `includes`. Boot does **not** pass those. Putting `routes` on an application entry does nothing until you call `Integrator::integrate()` yourself.

```json
{
    "id": "spanish",
    "match": ["https://es.example.com/*"],
    "locale": "es",
    "config": {
        "app": {"name": "Corrientes"},
        "streams": {"core": {"data_path": "streams/data/es"}}
    }
}
```

`match` is compared with `Str::is` against `Request::fullUrl()`, which includes the scheme. A pattern of `es.example.com/*` does not match `https://es.example.com/…`. The example in Core's `applications.json` omits the scheme; include it.

`Integrator::config()` flattens the array with `Arr::dot`, runs string values that contain `}` through `Str::parse`, then `Config::set()` on the dotted keys. Nested config in the file is the right shape. A flat key that already contains a dot, such as `"app.name"`, is also set as that config path.

```php
use Streams\Core\Support\Facades\Applications;

Applications::active();              // the entry chosen for this request
Applications::activate($other);      // replace it; does not re-run Integrator
```

Calling `activate()` later does not merge that entry's config again. Integration runs once during boot.

## API

`API::tenant()` and `ApiInterface::tenant()` store a resolver. `API::getTenant()` calls it once per request and binds the result as `streams.api.tenant`. List and show endpoints do not apply it. You listen for their `apply` callback, or you enforce the tenant in gate middleware. Create, update, delete, and query are not covered by that callback. The working pattern is on [API tenancy](/docs/api/tenancy).

## UI panels

`Panel::tenant($value)` stores a value or closure on the panel, and `Streams\Ui\UiManager::tenant()` stores a closure whose `getTenant()` result is available during the request. Resource and page route code that would put that tenant into route parameters is commented out, so a panel tenant does not change URLs or scope entry queries.

Use applications or the API resolver when you need isolation. Use the panel method only when your own page code reads `UI::getTenant()` or `$panel->getTenant()`.

## Related

- [Applications](/docs/core/applications)
- [Integrator](/docs/core/integrator)
- [API tenancy](/docs/api/tenancy)
- [Authentication](/docs/api/authentication)
