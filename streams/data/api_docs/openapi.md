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

A generic OpenAPI 3 description of the built-in endpoints is published at [/docs/api/openapi.yaml](/docs/api/openapi.yaml). It covers every route on `StreamsResource` and `EntriesResource`, the query parameters, and the response envelope. Entry bodies are generic there because they depend on your streams.

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
