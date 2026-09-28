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
 *     composer local                         # write composer.local.json and update streams/*
 *     composer local -- --rc=core,sdk        # link core and sdk from ../_rc instead
 *
 *     php scripts/composer-local.php [--rc[=core,sdk]] [--update]
 *     COMPOSER=composer.local.json composer update "streams/*"
 *
 * Go back to the GitHub packages with a plain `composer install`.
 *
 * Checkouts are looked up in this order (the first that exists wins):
 *   STREAMS_<NAME>_PATH env var, e.g. STREAMS_CORE_PATH=../streams-core
 *   ../_packages/streams-<name>
 *   ../streams-<name>
 *
 * ../_rc/streams-<name> (release-candidate worktrees) is used only when
 * asked for: --rc (every package) or --rc=core,sdk (those packages), or the
 * same list in STREAMS_LOCAL_RC (STREAMS_LOCAL_RC=1 means every package).
 *
 * --update runs `composer update "streams/*"` against composer.local.json
 * afterwards (the `composer local` script passes it).
 */

$root = dirname(__DIR__);
$composer = json_decode(file_get_contents($root . '/composer.json'), true, 512, JSON_THROW_ON_ERROR);

$update = false;
$rc = array_filter(explode(',', (string) getenv('STREAMS_LOCAL_RC')));

foreach (array_slice($argv, 1) as $argument) {
    if ($argument === '--update') {
        $update = true;
    } elseif ($argument === '--rc') {
        $rc = ['1'];
    } elseif (str_starts_with($argument, '--rc=')) {
        $rc = array_filter(explode(',', substr($argument, strlen('--rc='))));
    } else {
        fwrite(STDERR, "Unknown option {$argument}. Use --rc, --rc=core,sdk or --update.\n");
        exit(1);
    }
}

$useRc = fn (string $name): bool => in_array($name, $rc, true)
    || array_intersect($rc, ['1', 'all', 'true']) !== [];

$constraints = ($composer['require'] ?? []) + ($composer['require-dev'] ?? []);
$repositories = [];

foreach ($constraints as $package => $constraint) {
    if (! str_starts_with($package, 'streams/')) {
        continue;
    }

    $name = substr($package, strlen('streams/'));
    $candidates = array_filter([
        getenv('STREAMS_' . strtoupper($name) . '_PATH') ?: null,
        $useRc($name) ? '../_rc/streams-' . $name : null,
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

    // A dev-<branch> constraint names the branch the site is tested against.
    // Warn when the checkout does not contain it (for example _packages on
    // 2.0 while composer.json wants rc/prep).
    $absolute = str_starts_with($path, '/') ? $path : $root . '/' . $path;
    if (str_starts_with($version, 'dev-') && file_exists($absolute . '/.git')) {
        $branch = substr($version, strlen('dev-'));
        $ref = escapeshellarg('origin/' . $branch);
        $dir = escapeshellarg($absolute);
        exec("git -C {$dir} rev-parse --verify --quiet {$ref} 2>/dev/null", $output, $exists);
        if ($exists === 0) {
            exec("git -C {$dir} merge-base --is-ancestor {$ref} HEAD 2>/dev/null", $output, $contains);
            if ($contains !== 0) {
                fwrite(STDOUT, "    warning: {$path} does not contain origin/{$branch}. Check out or merge {$branch} there, or use --rc={$name} to link ../_rc/streams-{$name}.\n");
            }
        }
    }
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

if (! $update) {
    fwrite(STDOUT, "\nWrote composer.local.json. Now run:\n  COMPOSER=composer.local.json composer update \"streams/*\"\n(or run `composer local`, which does both).\n");
    exit(0);
}

fwrite(STDOUT, "\nWrote composer.local.json. Updating streams/* from it...\n");

// Inside `composer local`, Composer exports its own path as COMPOSER_BINARY.
$binary = getenv('COMPOSER_BINARY') ?: 'composer';
$command = (str_contains($binary, '/') && ! is_executable($binary) ? escapeshellarg(PHP_BINARY) . ' ' : '')
    . escapeshellarg($binary) . ' update ' . escapeshellarg('streams/*') . ' --no-interaction';

putenv('COMPOSER=composer.local.json');
chdir($root);
passthru($command, $status);
exit($status);
