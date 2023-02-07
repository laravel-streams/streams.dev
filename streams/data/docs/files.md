---
title: Files
description: 'Files and image handling.'
sort_order: 3
category: basics
status: ideation
---

## Introduction

Streams comes with a simple wrapper for the Laravel filesystem.

### Files Stream

First, define a stream for the filesystem data:

```json
{
    "name": "Files",
    "handle": "files",
    "description": "Basic filesystem cache.",
    "config": {
        "source": {
            "format": "json"
        }
    },
    "fields": [
        {
            "handle": "id",
            "type": "uuid",
            "required": true,
            "unique": true,
            "config": {
                "default": true
            }
        },
        {
            "handle": "path",
            "type": "string",
            "required": true
        },
        {
            "handle": "is_dir",
            "type": "boolean",
            "required": true
        },
        {
            "handle": "disk",
            "type": "string",
            "required": true
        },
        {
            "handle": "name",
            "type": "string",
            "required": true
        },
        {
            "handle": "size",
            "type": "integer"
        },
        {
            "handle": "mime_type",
            "type": "string"
        },
        {
            "handle": "visibility",
            "type": "string"
        },
        {
            "handle": "last_modified",
            "type": "datetime"
        },
        {
            "handle": "extension",
            "type": "string"
        }
    ]
}
```

### Configuration

To get started, define the data stream for the filesystem disk you wish to use.

```php
// config/filesystems.php
// ...
'disks' => [
    'local' => [
        'stream' => 'files',
    ],
],
```


## Filesystem Wrapper

The `StreamFilesystem` decorates Laravel's configured filesystem. In order to leverage streams fully you will need to use this wrapper to interact with the filesystem. The wrapper exists only to sync filesystem data to the configured stream.

```php
$filesystem = Streams::filesystem(string $disk);

// If you happen to have the stream.
$filesystem = $stream->filesystem(string $disk);

// Use as normal.
$filesystem->put($path, $content);
```

### Indexing Files

Use the `index` method to index the filesystem to the configured stream.

```php
$filesystem->index(string $path = '/');
```

### Filesystem Data

After indexing, the filesystem data can be used normally.

```php
$images = Streams::entries('files')
    ->where('extension', 'jpg')
    ->orderBy('size', 'asc')
    ->get();
```
