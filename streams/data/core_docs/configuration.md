---
title: Configuration
description: 'config/streams/core.php — data path, sources, field types, and images.'
sort_order: 2
status: ready
---

Core configuration lives in `config/streams/core.php`, published from the package. Environment variables override defaults for deployment-specific paths and sources.

## Key settings

| Key | Env | Default | Purpose |
|-----|-----|---------|---------|
| `streams_id` | — | `streams` | Meta-stream storing stream definitions |
| `applications_id` | — | `applications` | Multi-app configuration stream |
| `data_path` | `STREAMS_DATA_PATH` | `streams/data` | Filebase entry directory |
| `default_source` | `STREAMS_SOURCE` | `filebase` | Default adapter when omitted in stream JSON |
| `auto_alt` | — | `true` | Generate image alt text when missing |
| `version_images` | — | `true` | Append cache-busting version to image URLs |

## Source formats

Under `sources.filebase.formats`, Core registers parsers for filebase entries:

| Format | Class |
|--------|-------|
| `json` | `Json` |
| `yaml` | `Yaml` |
| `html` | `Html` |
| `md` | `Markdown` |
| `tpl` | `Template` |

Set per-stream via `config.source.format`.

## Field types

The `field_types` array maps handle strings to PHP field type classes. Register custom types here when extending Core:

```php
'field_types' => [
    'string' => \Streams\Core\Field\Types\StringFieldType::class,
    // ...
    'my_type' => \App\Streams\MyFieldType::class,
],
```

See [Fields](/docs/core/fields) for registered handles.

## Per-stream config

Individual streams override global defaults in their JSON `config` block:

```json
{
    "config": {
        "source": { "type": "filebase", "format": "md" },
        "cache": { "enabled": true, "ttl": 3600, "store": "redis" }
    }
}
```

See [Caching](/docs/core/caching) and [Sources and adapters](/docs/core/sources-and-adapters).

## Related

- [Installation](/docs/core/installation)
- [Streams](/docs/core/streams)
- [Extending Core](/docs/core/extending-core)
