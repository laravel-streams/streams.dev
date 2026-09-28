---
title: 'Core: Fields'
nav_title: Fields
description: Field handles, rules, shorthands, and registered types from core.php.
section: packages
package: core
order: 50
tags: [core, fields]
status: ready
---

Fields define entry attributes on a stream. Each field has a **handle**, **type**, and optional validation, defaults, and UI input config.

## Definition formats

**Shorthand map:**

```json
"fields": {
    "title": "string",
    "published": "boolean"
}
```

**Full object:**

```json
{
    "handle": "slug",
    "type": "slug",
    "required": true,
    "unique": true,
    "rules": ["alpha_dash"],
    "protected": false
}
```

## Common field options

| Option | Purpose |
|--------|---------|
| `required` | Adds required validation rule |
| `unique` | Unique within stream (via StreamsPresenceVerifier) |
| `rules` | Additional Laravel validation rules |
| `protected` | Omit from `toArray()` / `toJson()` |
| `config.default` | Default value via factory |
| `input` | Admin UI hints (used by Streams UI) |

## Registered types

From `config/streams/core.php` `field_types`:

| Category | Types |
|----------|-------|
| Numbers | `number`, `integer`, `decimal` |
| Strings | `string`, `url`, `uuid`, `hash`, `slug`, `email`, `encrypted` |
| Boolean | `boolean` |
| Dates | `datetime`, `date`, `time` |
| Selection | `enum`, `select`, `multiselect` |
| Structured | `array`, `object`, `relationship` |
| Media | `file`, `image` |

There is no `text` type — use `string` with a textarea input config for long content.

## Accessing values

```php
$entry->title;
$entry->setAttribute('title', 'Hello');
$entry->decorate('body'); // formatted output
```

## Related

- [Field decorators](/docs/core/field-decorators)
- [Validation](/docs/core/validation)
- [Hub: Fields](/docs/fields)
