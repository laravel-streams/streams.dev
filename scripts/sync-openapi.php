<?php

/**
 * Copy the streams/api OpenAPI reference into this site.
 *
 * /docs/api/openapi.yaml serves resources/openapi/openapi.yaml, which is a
 * copy of resources/openapi/openapi.yaml in streams/api (this site does not
 * install streams/api, so it can't read it from vendor). Edit the spec in
 * streams/api, then re-copy it here:
 *
 *     php scripts/sync-openapi.php [path/to/streams-api]
 *
 * Without an argument the checkout is looked up in this order:
 *   STREAMS_API_PATH env var
 *   ../_rc/streams-api
 *   ../_packages/streams-api
 *   ../streams-api
 */

$root = dirname(__DIR__);

$candidates = array_filter([
    $argv[1] ?? null,
    getenv('STREAMS_API_PATH') ?: null,
    $root . '/../_rc/streams-api',
    $root . '/../_packages/streams-api',
    $root . '/../streams-api',
]);

foreach ($candidates as $candidate) {
    if (is_file($source = rtrim($candidate, '/') . '/resources/openapi/openapi.yaml')) {
        break;
    }

    $source = null;
}

if (! $source) {
    fwrite(STDERR, "No streams/api checkout with resources/openapi/openapi.yaml found.\n");
    exit(1);
}

$checkout = dirname($source, 3);
$git = 'git -C ' . escapeshellarg($checkout);
$branch = trim((string) shell_exec("{$git} rev-parse --abbrev-ref HEAD 2>/dev/null")) ?: 'unknown';
$commit = trim((string) shell_exec("{$git} rev-parse --short HEAD 2>/dev/null")) ?: 'unknown';

$header = <<<YAML
# Copied from streams/api (laravel-streams/streams-api) resources/openapi/openapi.yaml,
# branch {$branch} at {$commit}. Don't edit it here: change it in streams/api, then
# re-copy it with `php scripts/sync-openapi.php`. Served at /docs/api/openapi.yaml.

YAML;

file_put_contents($root . '/resources/openapi/openapi.yaml', $header . file_get_contents($source));

echo "Copied {$source} ({$branch} {$commit}) to resources/openapi/openapi.yaml\n";
