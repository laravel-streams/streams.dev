---
title: UI Playground
nav_title: UI Playground
description: Preview the planned workflow for configuring, sharing, and testing Streams UI examples.
section: guides
category: frontend
package: ui
order: 90
tags: [ui, playground, json, testing]
status: draft
---

<span class="st-pill">Planned</span>

The UI Playground is a planned sandbox for trying Streams UI configuration with sample data. This guide is written ahead of the implementation. The editor, rendered preview, share links, and test controls described below are not available yet. Each unbuilt workflow carries a **Planned** badge.

The intended scope is UI examples without a real database. For the UI features you can use today, start with [UI quick start](/docs/ui/quick-start), [Forms](/docs/ui/forms), and [Tables](/docs/ui/tables).

## Open the Playground

<span class="st-pill">Planned</span>

A dedicated Playground entry point is planned. There is no working Playground URL to open yet. The existing `/ui-test` route displays a placeholder page, without an editor or interactive preview.

Once the entry point ships, this section will provide its URL and the steps for opening a sample. Until then, use the [local development guide](/docs/local-development) to run the documentation site; it does not enable the planned controls.

## Edit JSON

<span class="st-pill">Planned</span>

The intended workflow is to open a sample, edit its JSON configuration, and inspect the resulting UI preview. JSON editing and preview updates are not implemented. The Playground configuration format and validation feedback will be documented here when they are available.

Do not assume that stream JSON is already a Playground input format or that UI forms are automatically generated from it. Current [Forms](/docs/ui/forms) use PHP builders. [Stream definitions](/docs/core/streams) describe content types; their schema is separate from the planned Playground editor contract.

## Share links

<span class="st-pill">Planned</span>

Sharing a configured example by link is planned. Link creation and restoring an example from a shared link are not implemented. No share URL format, persistence guarantee, or expiry behavior is defined here yet.

For now, share the configuration as text alongside the relevant [UI reference](/docs/ui/introduction) page. Use sample data and omit credentials and private records from examples.

## Test an example

<span class="st-pill">Planned</span>

Playground test controls and result reporting are planned. There is no in-browser test runner yet. When it ships, this section will describe the supported checks, how to run them, and how to interpret failures.

Today, test UI behavior in your Laravel application. Follow [Writing tests](/docs/testing/writing-tests) for the existing testing helpers, and run your application's tests with:

```bash
php artisan test
```

A future Playground result will not replace application tests for validation, authorization, and persistence.
