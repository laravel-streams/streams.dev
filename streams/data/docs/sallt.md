---
title: SALLT
nav_title: SALLT
description: 'Create a new Streams app with composer create-project, then streams:onboard.'
section: get-started
category: getting-started
package: all
order: 15
tags: [sallt, installation, onboard, getting-started]
editorial_status: ready
status: partial
tracking_task: pending
---

SALLT (**S**treams, **A**lpine.js, **L**aravel, **L**ivewire, **T**ailwind) is the preferred way to start a new Streams app. It is a Laravel 12 create-project that installs Core and the SDK, then runs onboard. Repository: [github.com/RyanThompson/SALLT](https://github.com/RyanThompson/SALLT).

For an existing Laravel app, use [Installation](/docs/installation) instead.

## Create the project

Not on Packagist yet — point Composer at GitHub:

```bash
composer create-project ryanthompson/sallt my-app \
  --repository='{"type":"vcs","url":"https://github.com/RyanThompson/SALLT"}'

cd my-app
```

That installs [`streams/core`](/docs/core/introduction) and [`streams/sdk`](/docs/sdk/introduction). After key generate, SQLite, and migrate, create-project runs:

```bash
php artisan streams:onboard
```

(`sallt:onboard` is an alias; `composer onboard` re-runs it.)

## Onboard and apply

The TUI writes `streams.onboard.json` (frontend, panels, tenancy, API, sample domain, and related options). Then emit what generators support today:

```bash
php artisan streams:onboard --apply
php artisan streams:onboard --defaults
php artisan streams:onboard --kitchen-sink --apply --force
php artisan streams:validate
```

`--apply` scaffolds domain plumbing first (model, repository, lang, `streams/{handle}.json`). UI, API, Alpine, and tenancy steps still depend on their generators and on requiring `streams/ui` / `streams/api` when needed. Recipe flags, the `todos` preset, and the golden walkthrough: [SDK: Onboard](/docs/sdk/onboard).

Path 2 (JS / front-end create-project) is not shipped; `php artisan streams:onboard --surface=js` only exercises the shared recipe shape.

## Related

- [Installation](/docs/installation) — existing apps; older `streams/streams` starter
- [SDK: Onboard](/docs/sdk/onboard)
- [Versions and support](/docs/versions)
