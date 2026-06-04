---
title: Pages
description: 'Custom panel pages in Streams UI.'
sort_order: 6
status: ready
---

## Overview

**Pages** are standalone panel views—dashboards, wizards, settings screens—not tied to a single stream CRUD resource.

Use pages when your team needs composed UI beyond auto-generated tables and forms.

## When to use a page

- Dashboard with metrics and shortcuts
- Multi-stream workflow (onboarding, imports)
- Product settings that span several forms

## Definition

Pages are typically PHP classes extending the UI page base, registered on the panel with a URI segment and navigation label.

```php
// Illustrative pattern — see your app's panel registration
$page = Page::make('settings')
    ->path('settings')
    ->title('Account Settings');
```

Pair with Livewire components or Blade views as you would in any Laravel app.

## Related

- [Panels](/docs/ui/panels)
- [Forms](/docs/ui/forms)
- [Components (hub)](/docs/components)
