---
title: OpenAPI
description: 'api:schema and api:documentation Artisan commands.'
sort_order: 15
status: ready
---

Generate OpenAPI schema and Swagger UI documentation from registered API routes.

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
