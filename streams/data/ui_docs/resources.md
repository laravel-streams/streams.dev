---
title: Resources
description: 'Stream resources in control panels.'
sort_order: 3
status: ready
---

## Overview

A **resource** connects a stream to panel UI: list (table), create/edit (form), and optional view page.

Resources are how ops and admin teams manage stream entries without custom CRUD for every model.

## Registering a resource

Attach a resource to a panel for a stream handle:

```php
// Illustrative — resource registration on a panel
$panel->resource('orders', [
    'table' => OrderTable::class,
    'form' => OrderForm::class,
]);
```

Streams UI can infer tables and forms from field definitions when you do not need custom builders.

## Customization

Override columns, filters, form layout, and policies in PHP when JSON defaults are not enough.

## Related

- [Panels](/docs/ui/panels)
- [Tables](/docs/ui/tables)
- [Forms](/docs/ui/forms)
- [Core entries](/docs/core/entries)
