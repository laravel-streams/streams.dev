---
title: 'SDK: Tap command'
nav_title: Tap command
description: Call an HTTP or HTTPS tap URL with optional query-string input.
section: packages
package: sdk
order: 80
tags: [sdk, commands]
status: implemented
tracking_task: 261
---

`streams:tap` is registered by `SdkServiceProvider` on SDK `1.0`. It makes an HTTP GET request to a tap URL and prints the response body:

```bash
php artisan streams:tap "https://example.com/tap?status=draft" "status=published&limit=10"
```

The signature is `streams:tap {tap} {input?}`. `tap` must be an HTTP or HTTPS URL. `input` is an optional query string; its keys override matching keys already in the URL. Omit it to use the URL’s existing query parameters.

The command exits successfully for a 2xx response. Invalid URLs, network failures, and any non-2xx response return a failure exit code. Redirects are not followed, so a 3xx response fails. A non-HTTP URL reports `The tap must be an http or https URL.`

See the [command reference](/docs/sdk/commands#streamstap) for the available commands.
