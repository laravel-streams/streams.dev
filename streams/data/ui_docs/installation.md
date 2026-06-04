---
title: Installation
description: 'Composer, UI::panel(Panel::make()), publish tags, and middleware.'
sort_order: 1
status: ready
---

Install UI alongside Core in your Laravel application.

## Require the package

```bash
composer require streams/ui
```

Ensure `streams/core` is already installed.

## Register a panel

In `AppServiceProvider::boot()`:

```php
use Streams\Ui\Support\Facades\UI;
use Streams\Ui\Builders\Panels\Panel;

UI::panel(
    Panel::make('admin')
        ->default()
        ->path('admin')
        ->brandName('My App')
        ->middleware(['web'])
);
```

## Publish tags

From `UiServiceProvider`:

```bash
php artisan vendor:publish --tag=config --provider="Streams\Ui\UiServiceProvider"
php artisan vendor:publish --tag=laravel-streams --provider="Streams\Ui\UiServiceProvider"
php artisan vendor:publish --tag=ui --provider="Streams\Ui\UiServiceProvider"
```

| Tag | Output |
|-----|--------|
| `config` | `config/streams/ui.php` |
| `laravel-streams` | `streams/` scaffold |
| `ui` | `resources/views/vendor/ui/` view overrides |

Package views use the `ui::` namespace. Assets register via `Assets::addPath('ui', ...)`.

## Middleware

UI registers middleware alias `panel` → `Streams\Ui\Http\Middleware\SetUpPanel`. Panel routes apply `panel:{id}` automatically.

## Related

- [Quick start](/docs/ui/quick-start)
- [Panels](/docs/ui/panels)
