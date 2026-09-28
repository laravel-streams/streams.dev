---
sort_order: 12
title: UI
description: 'Control panels and interface generation for Streams.'
category: advanced
status: ready
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
UI::panel(
    Panel::make('admin')->default()->path('admin')->middleware(['web'])
);
```

## Typical workflow

1. Define streams in `streams/` (Core).
2. Create Resource classes with `form()` and `table()` builders.
3. Register resources on the panel and visit `/admin`.

## Learn more

- [UI introduction](/docs/ui/introduction)
- [Quick start](/docs/ui/quick-start)
- [Panels](/docs/ui/panels)
- [Resources](/docs/ui/resources)
- [Forms](/docs/ui/forms)
- [Tables](/docs/ui/tables)
- [Control panel guide](/docs/control-panel)

See [Use cases](/docs/use-cases) for admin and product panel paths.
