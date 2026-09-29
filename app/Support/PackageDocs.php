<?php

namespace App\Support;

use RuntimeException;
use Symfony\Component\Process\Process;

/**
 * Where a package's reference docs are copied to and from.
 * Installed and not-installed packages use the same site folder.
 */
class PackageDocs
{
    /**
     * @var array<string, array{branch: string, candidates: list<string>}>
     */
    public const PACKAGES = [
        'core' => [
            'branch' => 'rc/prep',
            'candidates' => ['../_rc/streams-core', '../_packages/streams-core', '../streams-core'],
        ],
        'ui' => [
            'branch' => '1.0',
            'candidates' => ['../_rc/streams-ui', '../_packages/streams-ui', '../streams-ui'],
        ],
        'api' => [
            'branch' => '1.0',
            'candidates' => ['../_rc/streams-api', '../_packages/streams-api', '../streams-api'],
        ],
        'sdk' => [
            'branch' => 'sdk/rc',
            'candidates' => ['../_rc/streams-sdk', '../_packages/streams-sdk', '../streams-sdk'],
        ],
        'testing' => [
            'branch' => '1.0',
            'candidates' => ['../_rc/streams-testing', '../_packages/streams-testing', '../streams-testing'],
        ],
        'client' => [
            'branch' => 'master',
            'candidates' => ['../_packages/api-client', '../api-client'],
        ],
    ];

    /**
     * @return list<string>
     */
    public static function names(): array
    {
        return array_keys(static::PACKAGES);
    }

    public static function siteDirectory(string $package): string
    {
        static::assertKnown($package);

        $root = getenv('STREAMS_DOCS_ROOT');
        $root = ($root !== false && $root !== '') ? $root : base_path('docs/packages');

        return rtrim($root, '/').'/'.$package;
    }

    /**
     * Checkout on the branch this site documents.
     * STREAMS_{NAME}_PATH, when set, is the only place that is considered.
     */
    public static function checkout(string $package): string
    {
        static::assertKnown($package);

        $expected = static::PACKAGES[$package]['branch'];
        $env = getenv('STREAMS_'.strtoupper($package).'_PATH');
        $candidates = ($env !== false && $env !== '')
            ? [$env]
            : static::PACKAGES[$package]['candidates'];

        $missing = [];
        $wrong = [];

        foreach ($candidates as $candidate) {
            $absolute = static::absolute($candidate);

            if (! static::isCheckout($absolute)) {
                $missing[] = $absolute;

                continue;
            }

            if (static::insideVendor($absolute)) {
                throw new RuntimeException("Refusing to write package docs inside vendor: {$absolute}");
            }

            $branch = static::branch($absolute);

            if ($branch !== $expected) {
                $wrong[] = "{$absolute} is on {$branch}";

                continue;
            }

            return $absolute;
        }

        $message = "No {$package} checkout is on {$expected}.";

        if ($wrong !== []) {
            $message .= ' '.implode('; ', $wrong).'.';
        } elseif ($missing !== []) {
            $message .= ' Looked in '.implode(', ', $missing).'.';
        }

        throw new RuntimeException($message);
    }

    /**
     * Refuse to overwrite a docs directory that already has uncommitted work.
     */
    public static function assertClean(string $directory): void
    {
        if (! is_dir($directory)) {
            return;
        }

        $top = static::gitTop($directory);
        $relative = static::relative($top, $directory);
        $process = new Process(['git', 'status', '--porcelain', '--', $relative], $top);
        $process->mustRun();

        $status = trim($process->getOutput());

        if ($status !== '') {
            throw new RuntimeException("{$directory} has uncommitted changes. Commit or stash them before syncing.");
        }
    }

    public static function branch(string $checkout): string
    {
        $process = new Process(['git', 'rev-parse', '--abbrev-ref', 'HEAD'], $checkout);
        $process->mustRun();

        return trim($process->getOutput());
    }

    public static function gitTop(string $directory): string
    {
        $start = is_dir($directory) ? $directory : dirname($directory);
        $process = new Process(['git', 'rev-parse', '--show-toplevel'], $start);
        $process->mustRun();

        $top = trim($process->getOutput());

        return realpath($top) ?: $top;
    }

    public static function relative(string $top, string $path): string
    {
        $top = rtrim(realpath($top) ?: $top, '/');
        $resolved = realpath($path);
        $path = rtrim($resolved !== false ? $resolved : $path, '/');

        if ($path === $top) {
            return '.';
        }

        if (! str_starts_with($path, $top.'/')) {
            throw new RuntimeException("{$path} is not inside {$top}.");
        }

        return substr($path, strlen($top) + 1);
    }

    protected static function assertKnown(string $package): void
    {
        if (! isset(static::PACKAGES[$package])) {
            throw new RuntimeException('Unknown package ['.$package.']. Expected one of: '.implode(', ', static::names()).'.');
        }
    }

    protected static function absolute(string $path): string
    {
        if (! str_starts_with($path, '/')) {
            $path = base_path($path);
        }

        $real = realpath($path);

        return $real !== false ? $real : $path;
    }

    protected static function isCheckout(string $path): bool
    {
        return is_dir($path.'/.git') || is_file($path.'/.git');
    }

    protected static function insideVendor(string $path): bool
    {
        $vendor = realpath(base_path('vendor'));
        $real = realpath($path);

        if ($vendor === false || $real === false) {
            return false;
        }

        return $real === $vendor || str_starts_with($real, $vendor.DIRECTORY_SEPARATOR);
    }
}
