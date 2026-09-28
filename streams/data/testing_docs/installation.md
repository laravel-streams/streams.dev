---
title: 'Testing: Installation'
nav_title: Installation
description: Install and configure the Streams Testing package.
section: packages
package: testing
order: 20
tags: [testing, installation]
status: ready
---

This guide will walk you through installing and configuring the Streams Testing package for your project.

## Requirements

Before installing, ensure your environment meets these requirements:

- **PHP**: 8.1 or higher
- **Laravel**: 10.x only. The package requires `orchestra/testbench ^8.36`, which targets Laravel 10.
- **Streams Core**: ^2.0
- **Composer**: Latest version recommended

## Installing the Package

Install the Streams Testing package via Composer as a development dependency:

```bash
composer require --dev streams/testing:1.0.x-dev
```

This will install the package and all its dependencies, including Orchestra Testbench and PHPUnit.

## Verifying Installation

After installation, verify everything is set up correctly:

```bash
vendor/bin/phpunit --version
```

You should see PHPUnit 10.x listed.

## Basic Configuration

### 1. Create phpunit.xml

If you don't already have a `phpunit.xml` file in your project root, create one:

```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:noNamespaceSchemaLocation="https://schema.phpunit.de/10.5/phpunit.xsd"
         bootstrap="vendor/autoload.php"
         colors="true"
         cacheDirectory=".phpunit.cache"
         displayDetailsOnTestsThatTriggerWarnings="true"
         failOnWarning="true"
         failOnRisky="true"
         beStrictAboutOutputDuringTests="true">
    <testsuites>
        <testsuite name="Application Tests">
            <directory suffix="Test.php">./tests</directory>
        </testsuite>
    </testsuites>
    <source>
        <include>
            <directory suffix=".php">./src</directory>
        </include>
    </source>
    <php>
        <env name="APP_ENV" value="testing"/>
        <env name="APP_KEY" value="base64:aiGINJ0oFnqrMGUwJYWJuhe6meZoW+GqppwDJD4YZeM="/>
    </php>
</phpunit>
```

### 2. Create Tests Directory

Create a `tests` directory if it doesn't exist:

```bash
mkdir tests
```

### 3. Write Your First Test

Create a test file `tests/ExampleTest.php`:

```php
<?php

namespace Tests;

use Streams\Core\Support\Facades\Streams;

class ExampleTest extends \Streams\Testing\TestCase
{
    public function test_basic_example()
    {
        $this->assertTrue(true);
    }
    
    public function test_streams_available()
    {
        $films = Streams::entries('films');
        
        $this->assertNotNull($films);
    }
}
```

### 4. Run Your Tests

Execute your test suite:

```bash
vendor/bin/phpunit
```

You should see output indicating your tests passed:

```text
PHPUnit 10.5.58 by Sebastian Bergmann and contributors.

..                                                                  2 / 2 (100%)

Time: 00:00.123, Memory: 24.00 MB

OK (2 tests, 2 assertions)
```

## Advanced Configuration

### Custom Test Namespace

Update your `composer.json` to use a custom test namespace:

```json
{
    "autoload-dev": {
        "psr-4": {
            "Tests\\": "tests/"
        }
    }
}
```

Then run:

```bash
composer dump-autoload
```

### IDE Configuration

#### PHPStorm / IntelliJ IDEA

1. Go to **Settings** → **PHP** → **Test Frameworks**
2. Click **+** and select **PHPUnit Local**
3. Set **Path to phpunit.phar** to `vendor/bin/phpunit`
4. Choose **Use Composer autoloader**
5. Set **Path to script** to `vendor/autoload.php`

#### VS Code

Install the "PHPUnit" extension and add to `.vscode/settings.json`:

```json
{
    "phpunit.php": "/usr/local/bin/php",
    "phpunit.phpunit": "vendor/bin/phpunit",
    "phpunit.args": [
        "--colors=always"
    ]
}
```

### CI/CD Integration

#### GitHub Actions

Create `.github/workflows/tests.yml`:

```yaml
name: Tests

on: [push, pull_request]

jobs:
  test:
    runs-on: ubuntu-latest
    
    steps:
      - uses: actions/checkout@v3
      
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: 8.2
          extensions: dom, curl, libxml, mbstring, zip
          coverage: none
      
      - name: Install Dependencies
        run: composer install --prefer-dist --no-interaction
      
      - name: Run Tests
        run: vendor/bin/phpunit
```

## Troubleshooting

### "Class not found" Errors

Regenerate the autoloader:

```bash
composer dump-autoload
```

### Memory Limit Issues

Increase PHP memory limit in `phpunit.xml`:

```xml
<php>
    <ini name="memory_limit" value="512M"/>
</php>
```

### Permission Errors

Ensure test data directories are writable:

```bash
chmod -R 775 vendor/streams/testing/laravel/streams
```

## Next Steps

Now that you have the package installed, learn about:

- [Test Data](/docs/testing/test-data) — sample streams
- [Writing Tests](/docs/testing/writing-tests)
- [Configuration](/docs/testing/configuration)
