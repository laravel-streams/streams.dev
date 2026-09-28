---
title: Versions and support
nav_title: Versions and support
description: Which Laravel and PHP versions each Streams package supports, and how to require them.
section: get-started
category: getting-started
package: all
order: 50
tags: [versions]
status: ready
---

Streams is pre-release. Most packages have **no tagged releases yet**, so you install them from their development branches. This page lists what each package declares in its `composer.json` (or `package.json`) and what it is tested against today.

## Support matrix

| Package | Install constraint | Branch | Laravel | PHP | Notes |
|---------|--------------------|--------|---------|-----|-------|
| [streams/core](/docs/core/introduction) | `2.0.x-dev` | `2.0` | `^10\|^11\|^12` | not declared (Laravel 10 needs 8.1+) | Stable tags exist only for the old 1.x line (latest `v1.10.4`). |
| [streams/ui](/docs/ui/introduction) | `1.0.x-dev` | `1.0` | via Core | via Core | Requires Livewire `^3.0`. |
| [streams/api](/docs/api/introduction) | `1.0.x-dev` | `1.0` | via Core | via Core | |
| [streams/sdk](/docs/sdk/introduction) | `1.0.x-dev` | `1.0` | via Core | via Core | Install as a dev dependency. |
| [streams/testing](/docs/testing/introduction) | `1.0.x-dev` | `1.0` | **10 only** | 8.1+ | Requires `orchestra/testbench ^8.36`, which targets Laravel 10. |
| [streams/mongodb](/docs/core/sources-and-adapters) | `1.0.x-dev` | `1.0` | via Core | via Core | Experimental. Requires `mongodb/mongodb ^1.10`. |
| [@laravel-streams/api-client](/docs/client/introduction) | `3.0.0` | `master` | n/a | n/a | npm package, zero runtime dependencies. |
| `streams/streams` (starter) | `1.0.x-dev` | `1.0` | `^10.0` | `^8.0.2` declared | Pins Laravel 10; requires Core, UI, and API. |

"Via Core" means the package declares no framework or PHP constraint of its own and accepts whatever `streams/core ^2.0` accepts.

### What is actually tested

- Every package's lock file and CI harness runs on **Laravel 10** (10.49). The shared harness, `streams/testing`, is built on testbench 8, which is Laravel 10 only.
- Core **declares** Laravel 11 and 12 support, but no package test suite runs on them yet. Treat 11 and 12 as expected to work, not verified.
- This site (streams.dev) runs Laravel 10.50 with `streams/core 2.0.x-dev` and `streams/ui 1.0.x-dev`.

## Requiring the packages

Because there are no stable 2.x (Core) or 1.x (everything else) tags, Composer's default `minimum-stability: stable` won't find them. Worse, a bare `composer require streams/core` silently installs **Core 1.10.4**, the previous major version.

Require the development branches explicitly, and require Core alongside anything that depends on it:

```bash
composer require streams/core:2.0.x-dev streams/ui:1.0.x-dev
composer require streams/core:2.0.x-dev streams/api:1.0.x-dev
composer require --dev streams/sdk:1.0.x-dev
```

Or allow dev packages project-wide while still preferring stable releases of everything else:

```json
{
    "minimum-stability": "dev",
    "prefer-stable": true
}
```

After that, `^2.0` for Core and `^1.0` for the others resolve to the development branches.

## Versioning policy

The plan is to adopt [semantic versioning](https://semver.org) and tag stable releases (Core 2.0, and 1.0 for UI, API, SDK, and Testing) as part of the release-candidate work. Until tags exist:

- Development branches can change without notice. Pin a commit (`2.0.x-dev#abc1234`) if you need a fixed point.
- Breaking changes are listed in the [Changelog](/docs/changelog) and explained in the [Upgrade guide](/docs/upgrading).
- The JavaScript client is already semver-tagged on npm (`3.0.0`).

## Related

- [Installation](/docs/installation)
- [Upgrade guide](/docs/upgrading)
- [Changelog](/docs/changelog)
