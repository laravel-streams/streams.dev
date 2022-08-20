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
<!-- @todo need other examples (laragon, homestead) -->
```bash
composer create laravel/laravel newproject

cd newproject

composer require streams/core:2.0.x-dev

php artisan serve
```


### Additional Packages

You may also want to consider using the following packages:

- [streams/api](/docs/api/introduction)
- [streams/ui](/docs/ui/introduction)

<!-- ### Dev Packages

The following development tools are also included:

- [Streams Testing](/docs/testing) -->

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


## Updating
From within your project, use Composer to update individual packages:

```bash
composer update streams/core --with-dependencies
composer update streams/api --with-dependencies
composer update streams/ui --with-dependencies
```

You can update your entire project using **composer update**.
