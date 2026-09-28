---
title: Control Panel
nav_title: Control Panel
description: Building admin and product panels with Streams UI.
section: guides
category: advanced
package: ui
order: 30
tags: [ui, control, panel]
status: ready
---

## Overview

A control panel (CP) is the authenticated area where your team or customers manage stream data. Streams UI registers panel routes, navigation, and resources from configuration and PHP panel builders.

## Admin vs product panel

| Type | Audience | Example |
|------|----------|---------|
| Admin panel | Internal ops | Manage users, orders, content |
| Product panel | End customers | Account settings, project config |

Same UI package; different panel registration and auth.

## Steps

1. Require `streams/ui` and configure middleware (typically `web`, `auth`).
2. Define a panel with path prefix (e.g. `/admin`).
3. Attach stream resources—tables and forms for each stream.
4. Customize navigation groups and pages as needed.

## Learn more

- [UI panels](/docs/ui/panels)
- [SDK admin panels](/docs/sdk/admin-panels)
- [Use cases](/docs/use-cases)
