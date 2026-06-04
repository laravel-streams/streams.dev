---
sort_order: 17
title: Localization
description: 'Internationalization with Streams and Laravel.'
category: advanced
status: ready
---

## Overview

Use Laravel's localization for application strings (`lang/`, `__()`, `@lang`). Streams field labels and CP text can pull from translation files like any Blade view.

For multi-locale **content**, model locales as separate streams, localized fields, or related translation entries— whichever fits your team's CMS pattern.

## Laravel strings

Publish UI lang files when customizing control panel copy:

```bash
php artisan vendor:publish --provider="Streams\\Ui\\UiServiceProvider" --tag=lang
```

## Learn more

- [Laravel localization](https://laravel.com/docs/localization)
- [UI introduction](/docs/ui/introduction)
- [Content modeling](/docs/content)
