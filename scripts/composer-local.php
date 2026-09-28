<?php

/**
 * Local package development without editing composer.json.
 *
 * composer.json installs every Streams package from GitHub. If you keep the
 * packages checked out next to this repository, this script writes a
 * gitignored composer.local.json that puts path repositories (symlinks) in
 * front of those GitHub repositories, and copies composer.lock to
 * composer.local.lock so nothing else moves.
 *
 *     php scripts/composer-local.php
 *     COMPOSER=composer.local.json composer update "streams/*"
 *
 * Go back to the GitHub packages with a plain `composer install`.
 *
 * Checkouts are looked up in this order (the first that exists wins):
 *   STREAMS_<NAME>_PATH env var, e.g. STREAMS_CORE_PATH=../streams-core
 *   ../_rc/streams-<name>
 *   ../_packages/streams-<name>
 *   ../streams-<name>
 */

$root = dirname(__DIR__);
$composer = json_decode(file_get_contents($root . '/composer.json'), true, 512, JSON_THROW_ON_ERROR);

$constraints = ($composer['require'] ?? []) + ($composer['require-dev'] ?? []);
$repositories = [];

foreach ($constraints as $package => $constraint) {
    if (! str_starts_with($package, 'streams/')) {
        continue;
    }

    $name = substr($package, strlen('streams/'));
    $candidates = array_filter([
        getenv('STREAMS_' . strtoupper($name) . '_PATH') ?: null,
        '../_rc/streams-' . $name,
        '../_packages/streams-' . $name,
        '../streams-' . $name,
    ]);

    $path = null;
    foreach ($candidates as $candidate) {
        $absolute = str_starts_with($candidate, '/') ? $candidate : $root . '/' . $candidate;
        if (is_file($absolute . '/composer.json')) {
            $path = $candidate;
            break;
        }
    }

    if (! $path) {
        fwrite(STDOUT, "  {$package}: no local checkout, stays on GitHub\n");
        continue;
    }

    // "dev-rc/prep as 2.0.x-dev" -> "dev-rc/prep". Forcing the version lets
    // the checkout sit on any branch and still satisfy composer.json.
    $version = trim(explode(' as ', $constraint)[0]);

    $repositories[] = [
        'type' => 'path',
        'url' => $path,
        'options' => ['symlink' => true, 'versions' => [$package => $version]],
    ];

    fwrite(STDOUT, "  {$package}: {$path} ({$version})\n");
}

if (! $repositories) {
    fwrite(STDERR, "No local Streams checkouts found. Nothing to do.\n");
    exit(1);
}

$composer['repositories'] = array_merge($repositories, $composer['repositories'] ?? []);

file_put_contents(
    $root . '/composer.local.json',
    json_encode($composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n"
);

if (is_file($root . '/composer.lock')) {
    copy($root . '/composer.lock', $root . '/composer.local.lock');
}

fwrite(STDOUT, "\nWrote composer.local.json. Now run:\n  COMPOSER=composer.local.json composer update \"streams/*\"\n");
