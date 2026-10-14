---
title: 'SDK: Tap command'
nav_title: Tap command
description: streams:tap is an unregistered SDK command whose handler is empty.
section: packages
package: sdk
order: 80
tags: [sdk, commands]
status: planned
tracking_task: 261
---

`streams:tap` is the class `Streams\Sdk\Console\Commands\StreamsTap` on `sdk/rc`. The signature is:

```bash
php artisan streams:tap {tap} {input?}
```

`tap` is described as a tap URL. `input` is an optional query string. `handle()` is an empty method, and `SdkServiceProvider` does not register the class, so Artisan does not expose the command.

The [command reference](/docs/sdk/commands) lists it with the other unregistered commands. Use the registered commands there until this one has an implementation. `tracking_task` is 261.
