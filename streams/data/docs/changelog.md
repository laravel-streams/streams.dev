---
title: Changelog
nav_title: Changelog
description: Notable changes to the Streams packages, newest first, grouped by month.
section: get-started
category: getting-started
package: all
order: 60
tags: [changelog]
status: ready
---

Streams packages are pre-release and installed from development branches, so this log is organized by month and branch rather than by version. Entries come from each repository's commit history. Changes that need action on your side are marked **Breaking** and explained in the [Upgrade guide](/docs/upgrading).

## September 2026

- **SDK (`1.0` release candidate):** Added a local-development [MCP server](/docs/mcp) (`php artisan mcp:start streams`, needs `laravel/mcp` on Laravel 11.45+ or 12.41+) with 14 tools, 2 resources, and the `design-stream` prompt. Added [`streams:validate`](/docs/sdk/commands#streamsvalidate) and [`streams:list --json`](/docs/sdk/commands#streamslist), and the [stream definition schema](/docs/sdk/stream-schema), served at `/schema/streams.schema.json`. `streams:livewire` now generates Livewire 3 components in `App\Livewire`. **Breaking:** `streams:admin` was removed, and `make:stream` and `streams:livewire` refuse to overwrite files without `--force`. The SDK now requires PHP 8.2.
- **Core, API (release candidates):** Require PHP 8.2. Core supports the Laravel 11 and 12 filesystem contract and Carbon 3.
- **Docs:** streams.dev is now the single source of truth for package documentation. Added [Versions and support](/docs/versions), this changelog, the [Upgrade guide](/docs/upgrading), `/llms.txt`, `/llms-full.txt`, and raw markdown for every page (append `.md` to any docs URL). Also added [Agents](/docs/agents), [MCP](/docs/mcp) (then planned; it has since shipped in `streams/sdk`), [Workflows](/docs/workflows), [Tenancy](/docs/tenancy), [Theming](/docs/ui/theming), and the [SDK command reference](/docs/sdk/commands).
- **UI (`1.0`):** HTML attribute support on more components.

## August 2026

- **Core (`2.0`):** Added `elasticsearch` and `opensearch` source adapters built on a shared `AbstractSearchIndexAdapter`. Configure OpenSearch connections under `streams.core.opensearch`. The client libraries are suggested dependencies, not required. See [Sources and adapters](/docs/core/sources-and-adapters).
- **UI (`1.0`):** **Breaking.** The OpenSearch adapter moved from UI to Core.
- **UI (`1.0`):** Accessible `ChartWidget` with an empty state; table search is ANDed with active filters; modal and file-input close actions.

## July 2026

- **Core (`2.0`):** **Breaking.** `Criteria::with()` attaches eager-loaded relationships under the relation name (the field handle without a trailing `_id`, or the field's `relation` config) and leaves the foreign-key attribute as a scalar.
- **API (`1.0`):** `with` / `with[]` eager loading matches relation names, following the Core change.
- **UI (`1.0`):** Bulk actions, pages and navigation updates, vertical navigation style, `MenuItem` modal support, image builder, icon-only actions, and button size/radius options.

## June 2026

- **API (`1.0`):** **Breaking.** Endpoint builders with explicit route registration. Controllers under `Streams\Api\Http\Controller\…` became invokable endpoints under `Streams\Api\Endpoints\…`; routes are mounted per `ApiInterface`; access runs through the configurable `gate_middleware`. See [Custom interfaces](/docs/api/custom-interfaces).
- **API (`1.0`):** `API::tenant()` and `ApiInterface::tenant()` for per-request tenant resolution. See [Tenancy](/docs/api/tenancy).
- **Core (`2.0`):** Two-argument `where('field', $value)` handling fix.
- **UI (`1.0`):** Table row selection fixes, row attributes, form spacing, required flags, and unified table filter state.

## May 2026

- **Core (`2.0`):** `Criteria::whereIn()`; `getIdAttribute()` on the Streams trait.
- **UI (`1.0`):** FullCalendar builder, modal header and footer components, grid IDs, bulk action improvements, and form state path prefixes.

## January to April 2026

- **UI (`1.0`):** Timeline component, panel wizard, breadcrumbs, `Action` extends `MountableAction`, file uploads on pages, and table views.
- **API (`1.0`):** Minor fixes to the entry show endpoint.
- **Client (`3.0.0`):** Tagged January 7 and published to npm from GitHub Actions. Version 3 is a zero-dependency JavaScript rewrite of the TypeScript client.

## Related

- [Upgrade guide](/docs/upgrading)
- [Versions and support](/docs/versions)
