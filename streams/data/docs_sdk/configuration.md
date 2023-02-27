---
title: Configuration
category: getting_started
intro: Configuring the SDK.
sort: 2
enabled: true
---

## Configuration Files

Published configuration files reside in `config/streams/`.

``` files
├── config/streams/
│   └── sdk.php
```

### Publishing Configuration

Use the following command to publish configuration files.

```bash
php artisan vendor:publish --provider=Streams\\Sdk\\SdkServiceProvider --tag=config
```

The above command will copy configuration files from their package location to the directory mentioned above so that you can modify them directly and commit them to your version control system.

## Configuration

Below are the contents of the published configuration file:

```php
// config/streams/sdk.php

return [

    /*
     * Determine if the SDK should be enabled.
     *
     * This is disabled by default because
     * The SDK is public by default.
     */
    'enabled' => env('STREAMS_SDK_ENABLED', true),

    /*
     * Specify the SDK prefix.
     */
    'prefix' => env('STREAMS_SDK_PREFIX', 'sdk'),

    /*
     * Specify the SDK group middleware.
     *
     * This is designed to match out of the box
     * "app/Providers/RouteServiceProvider.php"
     * and "app/Http/Kernel.php" Laravel files.
     *
     * Changing this value will require
     * adjusting the above files.
     */
    'middleware' => env('STREAMS_SDK_MIDDLEWARE', 'sdk'),

];
```

### SDK Middleware

SDK middleware an be configured in your application's HTTP kernel.

```php
// app/Http/Kernel.php

protected $middlewareGroups = [
    'sdk' => [
        'throttle:60,1',
        'bindings',
        Streams\Sdk\Http\Middleware\SdkCache::class,
    ],
];
```

### SDK Routes File

The `app/Providers/RouteServiceProvider.php` file typically uses the `sdk` middleware group when loading the `routes/sdk.php` file. By default this is compatible and routes defined there will be properly prefixed and grouped.
