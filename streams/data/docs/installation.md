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

For new projects, the fastest way to get up and running is the [Streams starter project](/packages/streams/streams):

```bash
composer create-project streams/streams:1.0.x-dev

cd streams

php artisan serve
```

### Included Packages

The following packages are installed with the starter project:

- [streams/core](/docs/core/introduction)
- [streams/api](/docs/api/introduction)
- [streams/ui](/docs/ui/introduction)
- [streams/sdk](/docs/sdk/introduction)

<!-- ### Dev Packages

The following development tools are also included:

- [Streams Testing](/docs/testing) -->

### Getting Started

Your streams project is now ready for you to start coding.

- [Configuration](configuration)
- [Debugging](debugging)


### The Basics

Explore the basics of Streams by example.

- [Defining Streams](how-to-define-streams)

#### Fundamental Concepts

Dig deeper into the fundamental concepts of Laravel Streams. 

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
