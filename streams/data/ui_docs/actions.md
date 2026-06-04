---
title: Actions
description: 'Table and form actions in Streams UI.'
sort_order: 7
status: ready
---

## Overview

**Actions** are buttons or links on tables and forms—row actions, bulk actions, header actions—that run closures, redirects, or Livewire handlers.

## Table row actions

Common patterns:

- View / Edit / Delete entry
- Custom workflow (approve, publish, refund)

Define actions on the table builder or in stream UI configuration.

## Bulk actions

Apply an operation to selected rows (delete, export, tag). Bulk actions respect authorization like single-row actions.

## Form actions

Submit, save and continue, cancel—configured on form builders alongside validation rules from stream fields.

## Related

- [Tables](/docs/ui/tables)
- [Forms](/docs/ui/forms)
