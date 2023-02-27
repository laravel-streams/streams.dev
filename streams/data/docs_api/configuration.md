---
title: Configuration
category: getting_started
intro: Configuring the API.
sort: 2
enabled: true
---

## Configuration Files

Published configuration files reside in `config/streams/`.

``` files
├── config/streams/
│   └── api.php
```

### Publishing Configuration

Use the following command to publish configuration files.

```bash
php artisan vendor:publish --provider=Streams\\Api\\ApiServiceProvider --tag=config
```

The above command will copy configuration files from their package location to the directory mentioned above so that you can modify them directly and commit them to your version control system.

## Configuring the API

Below are the contents of the published configuration file:

```php
// config/streams/api.php

return [

    /*
     * Determine if the API should be enabled.
     *
     * This is disabled by default because
     * The API is public by default.
     */
    'enabled' => env('STREAMS_API_ENABLED', false),

    /*
     * Specify the API prefix.
     */
    'prefix' => env('STREAMS_API_PREFIX', 'api'),

    /*
     * Specify the API group middleware.
     *
     * This is designed to match out of the box
     * "app/Providers/RouteServiceProvider.php"
     * and "app/Http/Kernel.php" Laravel files.
     *
     * Changing this value will require
     * adjusting the above files.
     */
    'middleware' => env('STREAMS_API_MIDDLEWARE', 'api'),
];
```

### API Middleware

API middleware an be configured in your application's HTTP kernel.

```php
// app/Http/Kernel.php

protected $middlewareGroups = [
    'api' => [
        'throttle:60,1',
        'bindings',
        \Streams\Api\Http\Middleware\ApiCache::class,
    ],
];
```

### API Routes File

The `app/Providers/RouteServiceProvider.php` file typically uses the `api` middleware group when loading the `routes/api.php` file. By default this is compatible and routes defined there will be properly prefixed and grouped.
