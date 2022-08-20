---
sort: 1
title: Installation
category: getting_started
stage: review
enabled: true
---


## Server Requirements
    
To run Laravel Streams you will need to meet [Laravel server requirements](https://laravel.com/docs/deployment#server-requirements).

### Supported Image Libraries

Please ensure one of the following libraries is installed in order to support [image manipulation](/docs/core/images).

- GD Library
- Imagick PHP extension


## New Projects

For new projects, the fastest way to get up and running is a new Laravel and Streams installation:

```bash
composer create laravel/laravel newproject

cd newproject

composer require streams/core:2.0.x-dev

php artisan serve
```


### Included Packages

The starter project is simply the latest Laravel with the following addons installed:

- [streams/core](/docs/core/introduction)
- [streams/api](/docs/api/introduction)
- [streams/ui](/docs/ui/introduction)

### Dev Packages

The following development tools are also included:

- [Streams Testing](/docs/testing)

### Via Composer Create-Project

```bash
composer create-project streams/streams:1.0.x-dev example.local --stability=dev
```

> There is no installer as there is no traditional database requirement.

### Local Development Server

If you have PHP installed locally and you would like to use PHP's built-in development server to serve your application, you may use the **serve** Artisan command to start a development server:

```bash
php artisan serve
```

### What's next?

Your streams project is now ready for you to start coding.

- [Configuration](configuration)
- [Debugging](debugging)

#### Ready to dive in?

- [Data Modeling](streams)
- [Laravel Development](core)
- [Frontend Development](frontend)
- [User Interface](ui)
- [API Readiness](api)

## Existing Laravel Projects

You can add the Streams platform to existing Laravel projects by requiring the packages you need.

### Streams Core

The **core** package is responsible for the meat and taters; it is the only **required** package.

```bash
composer require streams/core
```

#### To include UI features:

```bash
composer require streams/ui
```

#### To include API features:

```bash
composer require streams/api
```

#### Update Composer Scripts

This step is **optional**. You may find it helpful to compare our default **scripts** below to your own and decide what you would like to include.

```json
// composer.json
"scripts": {
    "pre-autoload-dump": [
        "rm -Rf bootstrap/cache/*.php"
    ],
    "post-autoload-dump": [
        "Illuminate\\Foundation\\ComposerScripts::postAutoloadDump",
        "@php artisan package:discover --ansi",
        "@php artisan clear --ansi",
        "@php artisan vendor:publish --ansi --tag=public",
        "@php artisan queue:restart --ansi"
    ],
    "post-root-package-install": [
        "@php -r \"file_exists('.env') || copy('.env.example', '.env');\""
    ],
    "post-create-project-cmd": [
        "@php artisan key:generate --ansi"
    ]
}
```


## Updating
From within your project, use Composer to update individual packages:

```bash
composer update streams/core --with-dependencies
composer update streams/api --with-dependencies
composer update streams/ui --with-dependencies
```

You can, of course, update your entire project using **composer update**.
