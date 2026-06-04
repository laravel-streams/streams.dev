---
sort_order: 12
title: UI
description: 'Control panels and interface generation for Streams.'
category: advanced
status: ready
---

## When to use Streams UI

Add `streams/ui` when your team needs an admin or product panel—forms, tables, navigation, and pages—without building Livewire components from scratch for every stream.

UI reads stream and panel configuration. You keep Laravel auth, policies, and middleware.

## Installation

```bash
composer require streams/ui
```

## Typical workflow

1. Define streams in `streams/` (Core).
2. Register a panel with navigation and resources.
3. Use generated forms and tables, or customize with PHP builders.

## Learn more

- [UI introduction](/docs/ui/introduction)
- [Panels](/docs/ui/panels)
- [Forms](/docs/ui/forms)
- [Tables](/docs/ui/tables)
- [Control panel guide](/docs/control-panel)

See [Use cases](/docs/use-cases) for admin and product panel paths.
