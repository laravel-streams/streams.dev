---
title: Applications
intro: Applicatons act as the main switchboard for incoming requests.
---
### Introduction
Applications serve as a primary entrypoint for Streams project. They collate configuration data and environment set ups, allowing Streams
to easily cater for multi-tenant applications.

### Configuration

Your application will run as `default`.. by default.

Furthermore, there _must_ be a default application though it doesn't have to use the default `handle`.

```php
'applications' => [
    'default' => [],
],
```
