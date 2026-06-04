---
sort_order: 4
title: Configuration
description: 'Configure the Streams Testing environment.'
status: ready
---

# Configuration

Learn how to configure the Streams Testing package to match your project's needs, from basic PHPUnit settings to advanced test environment customization.

## PHPUnit Configuration

### Basic phpunit.xml

The minimal configuration for Streams Testing:

```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:noNamespaceSchemaLocation="https://schema.phpunit.de/10.5/phpunit.xsd"
         bootstrap="vendor/autoload.php"
         colors="true">
    <testsuites>
        <testsuite name="Application Tests">
            <directory suffix="Test.php">./tests</directory>
        </testsuite>
    </testsuites>
</phpunit>
```

### Recommended Configuration

A more comprehensive setup with best practices:

```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:noNamespaceSchemaLocation="https://schema.phpunit.de/10.5/phpunit.xsd"
         bootstrap="vendor/autoload.php"
         colors="true"
         cacheDirectory=".phpunit.cache"
         displayDetailsOnTestsThatTriggerWarnings="true"
         displayDetailsOnTestsThatTriggerDeprecations="true"
         displayDetailsOnTestsThatTriggerNotices="true"
         failOnWarning="true"
         failOnRisky="true"
         beStrictAboutOutputDuringTests="true"
         executionOrder="random"
         cacheResult="true">
    <testsuites>
        <testsuite name="Unit">
            <directory suffix="Test.php">./tests/Unit</directory>
        </testsuite>
        <testsuite name="Feature">
            <directory suffix="Test.php">./tests/Feature</directory>
        </testsuite>
    </testsuites>
    <source>
        <include>
            <directory suffix=".php">./src</directory>
        </include>
        <exclude>
            <directory>./src/Console</directory>
            <file>./src/TestServiceProvider.php</file>
        </exclude>
    </source>
    <php>
        <ini name="display_errors" value="1"/>
        <ini name="error_reporting" value="-1"/>
        <ini name="memory_limit" value="512M"/>
        <env name="APP_ENV" value="testing"/>
        <env name="APP_KEY" value="base64:aiGINJ0oFnqrMGUwJYWJuhe6meZoW+GqppwDJD4YZeM="/>
        <env name="CACHE_DRIVER" value="array"/>
        <env name="SESSION_DRIVER" value="array"/>
        <env name="QUEUE_CONNECTION" value="sync"/>
    </php>
</phpunit>
```

### Configuration Options Explained

#### Test Execution

```xml
<phpunit
    executionOrder="random"          <!-- Run tests in random order -->
    stopOnError="false"              <!-- Continue after errors -->
    stopOnFailure="false"            <!-- Continue after failures -->
    stopOnWarning="false"            <!-- Continue after warnings -->
    stopOnRisky="false"              <!-- Continue after risky tests -->
    beStrictAboutOutputDuringTests="true"  <!-- Fail on output -->
>
```

#### Failure Handling

```xml
<phpunit
    failOnWarning="true"             <!-- Treat warnings as failures -->
    failOnRisky="true"               <!-- Treat risky tests as failures -->
    failOnDeprecation="false"        <!-- Don't fail on deprecations -->
    failOnNotice="false"             <!-- Don't fail on notices -->
>
```

#### Display Options

```xml
<phpunit
    colors="true"                    <!-- Use colored output -->
    displayDetailsOnTestsThatTriggerWarnings="true"
    displayDetailsOnTestsThatTriggerDeprecations="true"
    displayDetailsOnTestsThatTriggerNotices="true"
>
```

## Environment Variables

### Laravel Configuration

Configure Laravel behavior during tests:

```xml
<php>
    <!-- Application -->
    <env name="APP_ENV" value="testing"/>
    <env name="APP_DEBUG" value="true"/>
    <env name="APP_KEY" value="base64:your-key-here"/>
    
    <!-- Cache -->
    <env name="CACHE_DRIVER" value="array"/>
    
    <!-- Session -->
    <env name="SESSION_DRIVER" value="array"/>
    
    <!-- Queue -->
    <env name="QUEUE_CONNECTION" value="sync"/>
    
    <!-- Mail -->
    <env name="MAIL_MAILER" value="array"/>
    
    <!-- Database -->
    <env name="DB_CONNECTION" value="sqlite"/>
    <env name="DB_DATABASE" value=":memory:"/>
</php>
```

### Streams Configuration

Configure Streams-specific settings:

```xml
<php>
    <!-- Streams Settings -->
    <env name="STREAMS_CACHE" value="false"/>
    <env name="STREAMS_SOURCE" value="file"/>
    <env name="STREAMS_PATH" value="./storage/streams"/>
</php>
```

### PHP Settings

Adjust PHP behavior:

```xml
<php>
    <ini name="display_errors" value="1"/>
    <ini name="error_reporting" value="E_ALL"/>
    <ini name="memory_limit" value="512M"/>
    <ini name="max_execution_time" value="300"/>
    <ini name="date.timezone" value="UTC"/>
</php>
```

## Custom TestCase

### Extending the Base TestCase

Create your own base TestCase with custom functionality:

```php
<?php

namespace Tests;

use Illuminate\Support\Facades\Log;

class CustomTestCase extends \Streams\Testing\TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Disable logging during tests
        Log::spy();
        
        // Additional setup
        $this->artisan('cache:clear');
    }
    
    protected function tearDown(): void
    {
        // Custom cleanup
        
        parent::tearDown();
    }
    
    /**
     * Create a test film entry
     */
    protected function createFilm(array $attributes = []): object
    {
        return Streams::make('films')->create(array_merge([
            'title' => 'Test Film',
            'director' => 'Test Director',
            'release_date' => now()->format('Y-m-d'),
        ], $attributes));
    }
}
```

### Using Your Custom TestCase

```php
<?php

namespace Tests\Feature;

class FilmTest extends \Tests\CustomTestCase
{
    public function test_creates_film_using_helper()
    {
        $film = $this->createFilm(['title' => 'My Film']);
        
        $this->assertEquals('My Film', $film->title);
    }
}
```

## Test Organization

### Directory Structure

Organize tests by type:

```
tests/
├── Feature/           # Integration tests
│   ├── FilmTest.php
│   └── PlanetTest.php
├── Unit/              # Unit tests
│   ├── StreamTest.php
│   └── EntryTest.php
├── Fixtures/          # Test data
│   └── films.json
└── Support/           # Test helpers
    └── Helpers.php
```

### Autoloading Test Helpers

Add to `composer.json`:

```json
{
    "autoload-dev": {
        "psr-4": {
            "Tests\\": "tests/"
        },
        "files": [
            "tests/Support/helpers.php"
        ]
    }
}
```

## Code Coverage

### Enable Coverage

Install Xdebug or PCOV, then configure coverage:

```xml
<phpunit>
    <coverage processUncoveredFiles="true">
        <report>
            <html outputDirectory="coverage"/>
            <text outputFile="php://stdout"/>
        </report>
    </coverage>
    
    <source>
        <include>
            <directory suffix=".php">./src</directory>
        </include>
        <exclude>
            <directory>./src/Console</directory>
            <file>./src/TestServiceProvider.php</file>
        </exclude>
    </source>
</phpunit>
```

### Generate Coverage Report

```bash
# HTML report
vendor/bin/phpunit --coverage-html coverage

# Text report
vendor/bin/phpunit --coverage-text

# Clover XML (for CI)
vendor/bin/phpunit --coverage-clover coverage.xml
```

## Test Filtering

### Run Specific Test Suites

```bash
# Run only unit tests
vendor/bin/phpunit --testsuite Unit

# Run only feature tests
vendor/bin/phpunit --testsuite Feature
```

### Run Tests by Group

Add groups to tests:

```php
/**
 * @group films
 * @group integration
 */
public function test_film_creation()
{
    // Test code
}
```

Run by group:

```bash
# Run only films tests
vendor/bin/phpunit --group films

# Exclude slow tests
vendor/bin/phpunit --exclude-group slow
```

### Run Tests by Filter

```bash
# Run tests matching pattern
vendor/bin/phpunit --filter FilmTest

# Run specific test method
vendor/bin/phpunit --filter test_creates_film
```

## Performance Optimization

### Parallel Test Execution

Install ParaTest:

```bash
composer require --dev brianium/paratest
```

Run tests in parallel:

```bash
vendor/bin/paratest --processes=4
```

### Process Isolation

For tests that need isolation:

```xml
<phpunit processIsolation="true">
```

Or per-test:

```php
/**
 * @runInSeparateProcess
 */
public function test_isolated()
{
    // Test code
}
```

### Cache Configuration

Enable result caching:

```xml
<phpunit cacheDirectory=".phpunit.cache" cacheResult="true">
```

Add to `.gitignore`:

```
.phpunit.cache/
coverage/
```

## Database Configuration

### SQLite In-Memory

Fast testing with SQLite:

```xml
<php>
    <env name="DB_CONNECTION" value="sqlite"/>
    <env name="DB_DATABASE" value=":memory:"/>
</php>
```

### MySQL Testing Database

```xml
<php>
    <env name="DB_CONNECTION" value="mysql"/>
    <env name="DB_HOST" value="127.0.0.1"/>
    <env name="DB_PORT" value="3306"/>
    <env name="DB_DATABASE" value="testing"/>
    <env name="DB_USERNAME" value="root"/>
    <env name="DB_PASSWORD" value=""/>
</php>
```

## Continuous Integration

### GitHub Actions

`.github/workflows/tests.yml`:

```yaml
name: Tests

on: [push, pull_request]

jobs:
  test:
    runs-on: ubuntu-latest
    
    strategy:
      matrix:
        php: [8.1, 8.2, 8.3]
    
    steps:
      - uses: actions/checkout@v3
      
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: ${{ matrix.php }}
          extensions: dom, curl, libxml, mbstring, zip
          coverage: xdebug
      
      - name: Install Dependencies
        run: composer install --prefer-dist --no-interaction
      
      - name: Run Tests
        run: vendor/bin/phpunit --coverage-clover coverage.xml
      
      - name: Upload Coverage
        uses: codecov/codecov-action@v3
        with:
          files: ./coverage.xml
```

### GitLab CI

`.gitlab-ci.yml`:

```yaml
test:
  image: php:8.2
  
  before_script:
    - apt-get update -y
    - apt-get install -y git unzip
    - curl -sS https://getcomposer.org/installer | php
    - php composer.phar install
  
  script:
    - vendor/bin/phpunit
  
  artifacts:
    reports:
      junit: phpunit-report.xml
```

## Next Steps

- [Advanced Testing](advanced-testing.md) - Complex testing scenarios
- [CI/CD Integration](ci-cd.md) - Automated testing workflows
- [Troubleshooting](troubleshooting.md) - Common issues and solutions
