---
title: Use Cases
description: 'Starting paths for common product types with Streams.'
sort_order: 2
category: getting-started
status: ready
---

Pick the path closest to what your team is building. Each path lists packages to require and the first stream to define.

## Custom CMS or content site

**Packages:** `streams/core`, `streams/ui`

**First stream:** `pages` with filebase HTML or markdown entries and a `{path}` route.

Your team edits content as flat files or entries under `streams/data/`. UI provides an editorial control panel when you need one.

- [Content](/docs/content)
- [Routing](/docs/routing)
- [UI panels](/docs/ui/panels)

## SaaS product backend

**Packages:** `streams/core`, `streams/ui`, `streams/api`

**First stream:** a tenant-scoped domain model (e.g. `projects`, `subscriptions`) with Eloquent source.

Use Laravel auth and policies as usual. Expose a JSON API for your SPA or mobile clients; use UI for internal admin or customer account settings.

- [Users](/docs/users)
- [API introduction](/docs/api/introduction)
- [Databases](/docs/databases)

## Admin panel (internal ops)

**Packages:** `streams/core`, `streams/ui`

**First stream:** the primary entity your ops team manages (orders, users, inventory).

Define a panel in UI configuration with navigation, tables, and forms generated from stream fields.

- [Control panel](/docs/control-panel)
- [Forms](/docs/forms)
- [Tables](/docs/ui/tables)

## Product settings panel (customer-facing)

**Packages:** `streams/core`, `streams/ui`

**First stream:** settings or preferences owned by the authenticated user.

Smaller scope than a full admin CP—focused pages and wizards for your product's configuration surface.

- [UI pages](/docs/ui/pages)
- [Components](/docs/components)

## Headless API only

**Packages:** `streams/core`, `streams/api`

**First stream:** the resource you expose publicly or to partners.

Skip UI unless you later add an admin. Configure API interfaces, auth, and custom endpoints as needed.

- [API introduction](/docs/api/introduction)
- [Query parameters](/docs/api/query-parameters)
- [Client library](/docs/client/introduction)

## Greenfield starter

**Packages:** full starter via `composer create-project` (Core, UI, API, SDK)

Use the starter to learn conventions, then strip packages you do not need.

- [Installation](/docs/installation)
- [Addons](/docs/addons)
