---
title: OpenAPI
nav_title: OpenAPI
description: 'The generic OpenAPI reference, plus api:schema and api:documentation for your own app.'
section: packages
package: api
order: 180
tags: [api, openapi]
status: ready
---

## Reference spec

The `streams/api` package ships an OpenAPI 3 description of its built-in endpoints in `resources/openapi/openapi.yaml`, and this site serves a copy at [/docs/api/openapi.yaml](/docs/api/openapi.yaml). It covers every route on `StreamsResource` and `EntriesResource` (the paths `API::routeCrud()` registers), the query parameters, and the response envelope. The package's own tests check that every registered route is documented and nothing else. Entry bodies are generic there because they depend on your streams.

In your own app the file is at `vendor/streams/api/resources/openapi/openapi.yaml`. The copy on this site is re-synced from the package with `php scripts/sync-openapi.php`; its header comment names the branch and commit it came from.

## Your app's spec

Generate an OpenAPI document with per-stream schemas from your application's stream definitions.

`api:schema` builds a tag, a component schema, and the `/streams/{id}/entries` and `/streams/{id}/entries/{entry}` paths for every stream. It does not include the stream-definition endpoints, the query endpoint, or custom endpoints, and its `info` block (contact, license) is placeholder text you should edit before publishing.

## Dump schema

```bash
php artisan api:schema
```

Writes OpenAPI YAML via `ApiSchema::create()`. Default output: `api.yaml` at project root.

Custom path:

```bash
php artisan api:schema storage/api/openapi.yaml
```

## Swagger UI

```bash
php artisan api:documentation
```

Copies Swagger UI to `public/swagger/` and runs `api:schema public/swagger/api.yaml`.

View at: `http://your-app.test/swagger/index.html`

## Related

- [Routes](/docs/api/routes)
- [Examples](/docs/api/examples)
