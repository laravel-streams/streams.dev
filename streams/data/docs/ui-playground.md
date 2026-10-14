---
title: UI Playground
nav_title: UI Playground
description: Browse UI component references and learn about planned playground workflows.
section: guides
category: frontend
package: ui
order: 90
tags: [ui, playground, json, testing]
editorial_status: ready
status: partial
tracking_task: pending
---

The [UI Playground](/playground) renders each catalog component with the Streams UI package and the defaults shipped in the playground catalog. You can edit a component's example config as JSON and re-render the preview on the page. Share links and test controls are not available yet. Each unbuilt workflow carries a **Planned** badge.

The intended scope is UI examples without a real database. For the UI features you can use today, start with [UI quick start](/docs/ui/quick-start), [Forms](/docs/ui/forms), and [Tables](/docs/ui/tables).

## Open the Playground

Open [the Playground](/playground), or use the Playground link in the site header or footer to open it in a new tab. The component navigation opens a live render of that component and an editor for its example config.

Use the [local development guide](/docs/local-development) to run the documentation site and open `/playground` locally. The shell uses the site's saved light or dark theme.

Previews use in-memory defaults and the installed package's views. Builders without their own column view use the package's text-column view configured in the catalog; the image column currently renders the package's placeholder. These previews reflect the available package implementation. A component that throws while rendering shows a gap notice and logs the failure for investigation.

## Edit JSON

Open a component and edit its example config. The editor uses the streams make node shape: a `builder` class and the `defaults` passed to that builder's `make()` method. Nested components, actions, columns, and the card action use the same node.

```json
{
    "builder": "Streams\\Ui\\Builders\\Inputs\\TextInput",
    "defaults": {
        "name": "title",
        "label": "Title",
        "placeholder": "Enter a title"
    }
}
```

The editor validates that JSON against [catalog.schema.json](/schema/catalog.schema.json). Unknown keys are rejected. Invalid JSON and schema errors show beside the editor. A valid edit re-renders the preview on the same page, without a reload. The Playground does not save the configuration.

Do not assume that stream JSON is a Playground input. Current [Forms](/docs/ui/forms) use PHP builders. [Stream definitions](/docs/core/streams) describe content types; their schema is separate from the Playground node.

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
