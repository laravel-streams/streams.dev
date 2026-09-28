---
title: This project
nav_title: This project
description: What streams.dev is and how it uses Streams Core and UI.
section: contributing
category: this-project
package: site
order: 10
tags: [site, project]
status: ready
---

## What this repository is

**streams.dev** is the public documentation site and reference implementation for the Streams ecosystem. It is a Laravel 10 application that ships with **streams/core** and **streams/ui** in production, plus **streams/sdk** as a dev dependency.

Unlike the generic `composer create-project streams/streams` starter, this repo is purpose-built to:

- Serve documentation from flat files in `streams/data/`
- Demonstrate stream-driven pages (`/`, `/docs`, `/addons`) without custom controllers
- Link to package source via Composer path repositories for local development

## What is in git

| Path | Role |
|------|------|
| `streams/*.json` | Stream definitions (pages, docs, packages, categories) |
| `streams/data/` | Filebase content: markdown docs, HTML pages, package catalog data |
| `resources/views/` | Blade layouts and partials used by pages and docs |
| `app/Providers/AppServiceProvider.php` | Registers the admin panel via `UI::panel()` |
| `composer.json` | Requires Core + UI; path repos for sibling package clones |

There is minimal application code. Most behavior comes from stream configuration and package service providers.

## Packages in this project

Production dependencies:

- [streams/core](/docs/core/introduction) — data modeling, repositories, routing
- [streams/ui](/docs/ui/introduction) — Livewire admin panel at `/admin`

Dev-only:

- [streams/sdk](/docs/sdk/introduction) — scaffolding helpers

**streams/api** is available as a path repository for local work but is **not** required in this project's `composer.json`. Add it when you need REST endpoints.

## Related

- [Project structure](/docs/project-structure) — directory map
- [Local development](/docs/local-development) — run the site locally
- [Installation](/docs/installation) — add Streams to your own Laravel app
