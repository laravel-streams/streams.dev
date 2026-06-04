---
title: Panels
description: 'Register and configure control panels with Streams UI.'
sort_order: 2
status: ready
---

## Overview

A **panel** is a routed area of your application—typically `/admin` or `/account`—with its own navigation, middleware, and resources.

Register panels in PHP (panel builders) or stream-linked configuration depending on your app setup.

## Basic panel

```php
use Streams\Ui\Panels\Panel;

Panel::make('admin')
    ->path('admin')
    ->middleware(['web', 'auth'])
    ->brandName('Acme Admin');
```

Panel routes register under the configured path. Attach stream tables and forms as **resources**.

## Navigation

Group links in the sidebar:

- Stream resources (CRUD for each stream)
- Custom pages (dashboards, settings)
- External links

## Team conventions

- One panel for internal admin, a separate panel for customer account settings if both exist
- Share middleware and auth policies with the rest of your Laravel app
- Keep panel-specific assets published via `vendor:publish`

## Related

- [Control panel (hub)](/docs/control-panel)
- [Pages](/docs/ui/pages)
- [Resources](/docs/ui/resources)
- [SDK admin panels](/docs/sdk/admin-panels)
