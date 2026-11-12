---
title: Onboard
nav_title: Onboard
description: 'streams:onboard discovers a SALLT app and writes streams.onboard.json. --apply emits the golden todos monolith: API, Blade and Alpine, and a panel.'
section: packages
package: sdk
order: 15
tags: [sdk, onboard, sallt]
editorial_status: ready
status: implemented
tracking_task: null
---

`streams:onboard` is the create-project step after a Laravel shell has `streams/core` and `streams/sdk`. Start from [SALLT](/docs/sallt) for the create-project command; this page covers the recipe and `--apply`.

```bash
cd todos
php artisan streams:onboard --recipe=todos --apply
```

`--recipe` accepts a preset name shipped with the SDK (`todos`), a path to JSON, or `-` to read JSON from stdin. The TUI, `--kitchen-sink`, and that JSON all write the same `streams.onboard.json`. Unknown keys are rejected. The schema is `https://streams.dev/schema/onboard.schema.json` (also written by `streams:schema`).

## What `--apply` emits today

| Piece | Where |
|---|---|
| Domain plumbing | `app/Domains/{Domain}` model, repository, lang, `streams/{handle}.json` |
| Sample domains | Todos, plus Categories and Tags when the sample domain is Todos |
| Manage UI | Panel page, table, and modal add, compiled from the app tree |
| API | `API::routeEntries()` when `streams.api.enabled` is true. `STREAMS_API_STREAMS` is an allowlist; other handles return 404. `STREAMS_API_MIDDLEWARE=web` puts the API in the session/CSRF group for same-origin browser calls |
| Public page | `/` Blade view and `resources/js/todos.js` using `@laravel-streams/api-client` with `CsrfMiddleware` |
| Panel | `/admin` via `streams/ui`, with generated manage pages |
| Login | `auth` path `env-admin`: `/admin/login` checks `ADMIN_EMAIL` and `ADMIN_PASSWORD` in `.env.state` (seeded from the recipe apply, never rewritten into `.env`) |

`composer golden:check` in the todos app applies the committed recipe into a temp tree and diffs it against the repo.

## Walkthrough

The public golden app is [github.com/RyanThompson/todos](https://github.com/RyanThompson/todos).

1. Open `/` and add a todo. The page posts to `/api/streams/todos/entries` on the same origin.
2. Open `/admin/login`. The seeded admin is `admin@todos.test` / `password` (stored as a hash in `.env.state`).
3. After login, `/admin` lists manage pages for todos, categories, and tags. Add flows are modal actions on those pages.

Personal access tokens, a Postman collection, and an MCP toggle are later recipe flags. They are not emitted yet.

## Client

```js
import { Client, CsrfMiddleware } from '@laravel-streams/api-client';

const client = new Client({
    baseURL: '/api/',
    middlewares: [new CsrfMiddleware()],
});

await client.entries.get('todos');
```

`CsrfMiddleware` reads `meta[name=csrf-token]` or the `XSRF-TOKEN` cookie and sets `X-XSRF-TOKEN` on requests that are not GET or HEAD. The client defaults to `mode: 'same-origin'`.
