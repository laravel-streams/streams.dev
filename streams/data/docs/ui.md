---
title: UI
nav_title: UI
description: Control panels and interface generation for Streams.
section: guides
category: advanced
package: ui
order: 20
tags: [ui]
editorial_status: ready
status: implemented
tracking_task: 258
---

## When to use Streams UI

Add `streams/ui` when your team needs an admin or product panel—forms, tables, navigation, and pages—built with Livewire and PHP builders on top of Core streams.

You keep Laravel auth, policies, and middleware. Resources are PHP classes—not Blade helpers like `UI::form()`.

## Installation

```bash
composer require streams/core:2.0.x-dev streams/ui:1.0.x-dev
```

Register a panel in a service provider:

```php
use Streams\Ui\Builders\Panels\Panel;
use Streams\Ui\Support\Facades\UI;

UI::panel(
    Panel::make('admin')->default()->path('admin')->middleware(['web'])
);
```

## Typical workflow

1. Define streams in `streams/` (Core).
2. Create Resource classes with `form()` and `table()` builders.
3. Register resources on the panel and visit `/admin`.

The panel root redirects to `homeUrl()` when you set one, otherwise to the first navigation item. Register a navigable page or resource before visiting `/admin`; an empty panel has no destination and returns 404. You can also register a page or custom route at the panel root to render it directly.

Use `domain('admin.example.com')` or `domains([...])` to restrict a panel to configured hosts. Without a domain, the panel accepts any host. Its root, pages, resources, and custom `routes()` share the panel path and middleware. Custom route paths are relative to the panel path.

For a custom landing page, point `homeUrl()` at a route you register inside the panel:

```php
use Illuminate\Support\Facades\Route;
use Streams\Ui\Builders\Panels\Panel;
use Streams\Ui\Support\Facades\UI;

UI::panel(
    Panel::make('admin')
        ->default()
        ->path('admin')
        ->middleware(['web', 'auth'])
        ->homeUrl('/admin/dashboard')
        ->routes(function (Panel $panel): void {
            Route::view('/dashboard', 'admin.dashboard')->name('dashboard');
        })
);
```

Create the `admin.dashboard` Blade view for this example. Visiting `/admin` runs the panel middleware, selects the requested panel, and redirects to `/admin/dashboard`.

## Learn more

- [UI introduction](/docs/ui/introduction)
- [Quick start](/docs/ui/quick-start)
- [Panels](/docs/ui/panels)
- [Resources](/docs/ui/resources)
- [Forms](/docs/ui/forms)
- [Tables](/docs/ui/tables)
- [Control panel guide](/docs/control-panel)

See [Use cases](/docs/use-cases) for admin and product panel paths.
