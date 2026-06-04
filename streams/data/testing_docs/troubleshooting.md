---
sort_order: 5
title: Troubleshooting
description: 'Common issues when testing with Streams Testing.'
status: ready
---

# Troubleshooting

This guide helps you resolve common issues when using the Streams Testing package.

## Installation Issues

### Class Not Found

**Problem**: `Class 'Streams\Testing\TestCase' not found`

**Solution**:

```bash
# Regenerate autoload files
composer dump-autoload

# Clear any cached autoload files
rm -rf vendor/composer
composer install
```

### PHPUnit Not Found

**Problem**: `vendor/bin/phpunit: No such file or directory`

**Solution**:

```bash
# Reinstall dev dependencies
composer install --dev

# Or explicitly require PHPUnit
composer require --dev phpunit/phpunit
```

### Orchestra Testbench Missing

**Problem**: `Class 'Orchestra\Testbench\TestCase' not found`

**Solution**:

```bash
# Install compatible version
composer require --dev orchestra/testbench:^8.0
```

## Configuration Issues

### No Tests Found

**Problem**: PHPUnit reports `No tests executed!`

**Solutions**:

1. **Check phpunit.xml test suite configuration**:

```xml
<testsuites>
    <testsuite name="Tests">
        <directory suffix="Test.php">./tests</directory>
    </testsuite>
</testsuites>
```

2. **Verify test file naming**:
   - Files must end with `Test.php` (e.g., `FilmTest.php`)
   - Test methods must start with `test` or use `@test` annotation

3. **Check test directory exists**:

```bash
ls -la tests/
```

### Wrong PHPUnit Version

**Problem**: PHPUnit schema errors or deprecated features

**Solution**:

Check PHPUnit version matches configuration:

```bash
vendor/bin/phpunit --version
```

Update `phpunit.xml` schema:

```xml
<!-- For PHPUnit 10 -->
xsi:noNamespaceSchemaLocation="https://schema.phpunit.de/10.5/phpunit.xsd"

<!-- For PHPUnit 9 -->
xsi:noNamespaceSchemaLocation="https://schema.phpunit.de/9.3/phpunit.xsd"
```

### Missing APP_KEY

**Problem**: `No application encryption key has been specified`

**Solution**:

Add to `phpunit.xml`:

```xml
<php>
    <env name="APP_KEY" value="base64:aiGINJ0oFnqrMGUwJYWJuhe6meZoW+GqppwDJD4YZeM="/>
</php>
```

Or generate a new one:

```bash
php artisan key:generate --show
```

## Test Execution Issues

### Memory Limit Exceeded

**Problem**: `Fatal error: Allowed memory size exhausted`

**Solutions**:

1. **Increase memory limit in phpunit.xml**:

```xml
<php>
    <ini name="memory_limit" value="512M"/>
</php>
```

2. **Run with more memory**:

```bash
php -d memory_limit=512M vendor/bin/phpunit
```

3. **Use process isolation**:

```xml
<phpunit processIsolation="true">
```

### Tests Timeout

**Problem**: Tests hang or timeout

**Solutions**:

1. **Increase max execution time**:

```xml
<php>
    <ini name="max_execution_time" value="300"/>
</php>
```

2. **Check for infinite loops**:

```php
// Add timeouts to potentially long operations
$films = Streams::entries('films')
    ->timeout(30)
    ->get();
```

3. **Run with verbose output to identify hanging test**:

```bash
vendor/bin/phpunit --verbose --debug
```

### Permission Denied Errors

**Problem**: `Permission denied` when accessing test data

**Solution**:

```bash
# Make streams directories writable
chmod -R 775 vendor/streams/testing/laravel/streams
chmod -R 775 vendor/streams/testing/laravel/streams.bak

# Or change ownership
sudo chown -R $USER:$USER vendor/streams/testing/laravel/streams
```

## Test Data Issues

### Test Data Not Loading

**Problem**: Sample streams return empty results

**Solutions**:

1. **Verify test data exists**:

```bash
ls -la vendor/streams/testing/laravel/streams/
```

2. **Check backup data**:

```bash
ls -la vendor/streams/testing/laravel/streams.bak/
```

3. **Manually restore data**:

```php
public function setUp(): void
{
    parent::setUp();
    
    $this->restoreStreamsData();
}
```

4. **Verify stream files are valid JSON**:

```bash
php -r "json_decode(file_get_contents('vendor/streams/testing/laravel/streams/films.json'));"
```

### Data Not Resetting Between Tests

**Problem**: Modified data persists across tests

**Solutions**:

1. **Ensure parent tearDown is called**:

```php
protected function tearDown(): void
{
    // Your cleanup code here
    
    parent::tearDown(); // Must call this!
}
```

2. **Check file permissions**:

```bash
# Ensure test can write and delete
chmod -R 755 vendor/streams/testing/laravel/streams
```

3. **Manually verify restoration**:

```php
public function test_data_resets()
{
    $initialCount = Streams::entries('films')->count();
    
    Streams::make('films')->create(['title' => 'New Film']);
    
    $this->restoreStreamsData();
    
    $finalCount = Streams::entries('films')->count();
    $this->assertEquals($initialCount, $finalCount);
}
```

### Invalid JSON in Stream Files

**Problem**: `JSON decode error` when loading streams

**Solution**:

Validate and fix JSON:

```bash
# Validate JSON
cat vendor/streams/testing/laravel/streams/films.json | python -m json.tool

# Or use jq
jq . vendor/streams/testing/laravel/streams/films.json
```

## Assertion Issues

### Unexpected Test Failures

**Problem**: Tests fail unexpectedly

**Debug Steps**:

1. **Add debug output**:

```php
public function test_debug_example()
{
    $films = Streams::entries('films')->get();
    
    dump($films->count()); // Check actual count
    dd($films->toArray()); // Inspect full data
    
    $this->assertCount(7, $films);
}
```

2. **Use verbose assertions**:

```php
$this->assertEquals(
    $expected,
    $actual,
    "Expected $expected but got $actual"
);
```

3. **Run single test**:

```bash
vendor/bin/phpunit --filter test_specific_method --testdox
```

### Type Comparison Issues

**Problem**: `Expected integer but got string`

**Solution**:

Use appropriate assertions:

```php
// Loose comparison
$this->assertEquals(7, $count);

// Strict comparison
$this->assertSame(7, $count);

// Type checking
$this->assertIsInt($count);
$this->assertEquals(7, (int) $count);
```

### Collection vs Array Confusion

**Problem**: Assertions fail on collections

**Solution**:

Convert collections appropriately:

```php
// Get collection
$films = Streams::entries('films')->get();

// For counting
$this->assertCount(7, $films);

// For array assertions
$this->assertIsArray($films->toArray());

// For iteration
foreach ($films as $film) {
    $this->assertNotNull($film);
}
```

## Laravel Integration Issues

### Route Not Found in Tests

**Problem**: `Route [name] not defined`

**Solution**:

Ensure routes are loaded in TestCase:

```php
protected function getPackageProviders($app)
{
    return [
        \Your\Package\ServiceProvider::class,
    ];
}
```

### Config Not Loading

**Problem**: Configuration values not available

**Solution**:

Define config in TestCase:

```php
protected function defineEnvironment($app)
{
    $app['config']->set('streams.path', __DIR__ . '/streams');
}
```

### Service Provider Not Loaded

**Problem**: Services not registered

**Solution**:

Register providers explicitly:

```php
protected function getPackageProviders($app)
{
    return [
        \Streams\Core\StreamsServiceProvider::class,
        \Streams\Testing\TestServiceProvider::class,
    ];
}
```

## IDE Issues

### PHPStorm Not Running Tests

**Problem**: Can't run tests from IDE

**Solutions**:

1. **Configure PHPUnit**:
   - Settings → PHP → Test Frameworks
   - Add PHPUnit Local
   - Use Composer autoloader: `vendor/autoload.php`

2. **Mark directories**:
   - Right-click `tests/` → Mark Directory As → Test Sources Root

3. **Refresh configuration**:
   - File → Invalidate Caches / Restart

### VS Code Not Recognizing Tests

**Problem**: Test runner doesn't find tests

**Solutions**:

1. **Install PHPUnit extension**:
   - Better PHPUnit by calebporzio

2. **Configure settings.json**:

```json
{
    "phpunit.phpunit": "vendor/bin/phpunit",
    "phpunit.args": ["--colors=always"]
}
```

3. **Reload window**:
   - Cmd/Ctrl + Shift + P → Reload Window

## Performance Issues

### Tests Running Slowly

**Solutions**:

1. **Use process isolation sparingly**:

```xml
<!-- Only when needed -->
<phpunit processIsolation="false">
```

2. **Disable coverage when not needed**:

```bash
vendor/bin/phpunit --no-coverage
```

3. **Run tests in parallel**:

```bash
composer require --dev brianium/paratest
vendor/bin/paratest
```

4. **Use SQLite in-memory for database tests**:

```xml
<env name="DB_CONNECTION" value="sqlite"/>
<env name="DB_DATABASE" value=":memory:"/>
```

### High Memory Usage

**Solutions**:

1. **Clear data after tests**:

```php
protected function tearDown(): void
{
    $this->clearData();
    parent::tearDown();
}
```

2. **Use fewer fixtures**:

```php
// Load only needed data
$this->loadFixtures(['films']); // Not all streams
```

3. **Garbage collection**:

```php
protected function tearDown(): void
{
    gc_collect_cycles();
    parent::tearDown();
}
```

## Getting Help

### Diagnostic Information

When reporting issues, include:

```bash
# PHP version
php -v

# Composer info
composer show

# PHPUnit version
vendor/bin/phpunit --version

# Streams core version
composer show streams/core

# Run tests with verbose output
vendor/bin/phpunit --verbose --debug
```

### Enable Debug Mode

Add to `phpunit.xml`:

```xml
<php>
    <env name="APP_DEBUG" value="true"/>
    <ini name="display_errors" value="1"/>
    <ini name="error_reporting" value="E_ALL"/>
</php>
```

### Community Resources

- **Documentation**: https://streams.dev/docs/testing/introduction
- **GitHub Issues**: https://github.com/laravel-streams/streams-testing/issues
- **Discord**: Join the Streams community
- **Stack Overflow**: Tag with `laravel-streams`

## Quick Checklist

When tests aren't working, check:

- [ ] `composer dump-autoload` executed
- [ ] `phpunit.xml` exists and is valid
- [ ] Test files end with `Test.php`
- [ ] Test methods start with `test`
- [ ] Extending `\Streams\Testing\TestCase`
- [ ] `parent::setUp()` and `parent::tearDown()` called
- [ ] File permissions correct on stream data
- [ ] APP_KEY set in phpunit.xml
- [ ] Correct PHP version (8.1+)
- [ ] Dependencies installed with `composer install`

Still having issues? Check the [GitHub issues](https://github.com/laravel-streams/streams-testing/issues) or create a new one with the diagnostic information above.
