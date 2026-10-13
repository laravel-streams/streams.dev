<?php

namespace App\Console\Commands;

use App\Support\PackageDocs;
use Illuminate\Console\Command;
use Symfony\Component\Process\Process;
use Throwable;

class SyncDocs extends Command
{
    protected $signature = 'docs:sync
        {direction : push (this site onto the package) or pull (the package onto this site)}
        {package? : core, ui, api, sdk, testing, or client. Omit to sync all of them.}
        {--page= : Copy this one page and leave every other file alone}
        {--dry-run : Print the copy and delete list without writing}
        {--commit : Commit the markdown in the destination repository}
        {--push : Push that commit. Requires --commit.}';

    protected $description = 'Copy package reference markdown between docs/packages and the package repository.';

    public function handle(): int
    {
        $direction = (string) $this->argument('direction');

        if (! in_array($direction, ['push', 'pull'], true)) {
            $this->error('Direction must be push or pull.');

            return self::FAILURE;
        }

        if ($this->option('push') && ! $this->option('commit')) {
            $this->error('--push requires --commit.');

            return self::FAILURE;
        }

        $package = $this->argument('package');
        $names = $package ? [(string) $package] : PackageDocs::names();

        if ($this->option('page') && count($names) !== 1) {
            $this->error('--page requires a single package.');

            return self::FAILURE;
        }

        foreach ($names as $name) {
            try {
                $this->syncPackage($direction, $name);
            } catch (Throwable $exception) {
                $this->error($exception->getMessage());

                return self::FAILURE;
            }
        }

        return self::SUCCESS;
    }

    protected function syncPackage(string $direction, string $package): void
    {
        $checkout = PackageDocs::checkout($package);
        $site = PackageDocs::siteDirectory($package);
        $repositoryDocs = $checkout.'/docs';

        [$from, $to] = $direction === 'push'
            ? [$site, $repositoryDocs]
            : [$repositoryDocs, $site];

        PackageDocs::assertClean($to);

        $page = $this->option('page') ?: null;
        $plan = $this->plan($from, $to, $page !== null && $page !== '' ? (string) $page : null);

        $this->line(strtoupper($direction).' '.$package.' ('.$checkout.')');

        foreach ($plan['copy'] as $id) {
            $this->line('  copy '.$id.'.md');
        }

        foreach ($plan['delete'] as $id) {
            $this->line('  delete '.$id.'.md');
        }

        if ($plan['copy'] === [] && $plan['delete'] === []) {
            $this->line('  unchanged');
        }

        if ($this->option('dry-run')) {
            return;
        }

        if (! is_dir($to) && ! mkdir($to, 0755, true) && ! is_dir($to)) {
            throw new \RuntimeException("Could not create {$to}.");
        }

        foreach ($plan['copy'] as $id) {
            if (! copy($from.'/'.$id.'.md', $to.'/'.$id.'.md')) {
                throw new \RuntimeException("Could not copy {$id}.md into {$to}.");
            }
        }

        foreach ($plan['delete'] as $id) {
            if (! unlink($to.'/'.$id.'.md')) {
                throw new \RuntimeException("Could not delete {$to}/{$id}.md.");
            }
        }

        if ($this->option('commit')) {
            $this->commit($direction, $checkout, $package);
        }
    }

    /**
     * @return array{copy: list<string>, delete: list<string>}
     */
    protected function plan(string $from, string $to, ?string $page): array
    {
        $source = $this->markdown($from);
        $destination = $this->markdown($to);

        if ($page !== null) {
            if (! isset($source[$page])) {
                throw new \RuntimeException("Page [{$page}] is not in {$from}.");
            }

            $source = [$page => $source[$page]];
            $destination = isset($destination[$page]) ? [$page => $destination[$page]] : [];
        }

        $copy = [];

        foreach ($source as $id => $path) {
            $target = $to.'/'.$id.'.md';

            if (! is_file($target) || file_get_contents($target) !== file_get_contents($path)) {
                $copy[] = $id;
            }
        }

        $delete = $page !== null ? [] : array_keys(array_diff_key($destination, $source));

        return ['copy' => $copy, 'delete' => $delete];
    }

    /**
     * @return array<string, string>
     */
    protected function markdown(string $directory): array
    {
        $files = [];

        foreach (glob(rtrim($directory, '/').'/*.md') ?: [] as $file) {
            $files[basename($file, '.md')] = $file;
        }

        ksort($files);

        return $files;
    }

    protected function commit(string $direction, string $checkout, string $package): void
    {
        $directory = $direction === 'push'
            ? $checkout.'/docs'
            : PackageDocs::siteDirectory($package);

        $top = PackageDocs::gitTop($directory);
        $relative = PackageDocs::relative($top, $directory);

        $add = new Process(['git', 'add', '--', $relative], $top);
        $add->mustRun();

        $staged = new Process(['git', 'diff', '--cached', '--quiet', '--', $relative], $top);
        $staged->run();

        if ($staged->getExitCode() === 0) {
            $this->line('  nothing to commit');

            return;
        }

        $message = $direction === 'push'
            ? "Sync {$package} docs from streams.dev."
            : "Sync {$package} docs from the package repository.";

        $commit = new Process(['git', 'commit', '-m', $message], $top);
        $commit->mustRun();
        $this->line('  committed');

        if ($this->option('push')) {
            $push = new Process(['git', 'push'], $top);
            $push->mustRun();
            $this->line('  pushed');
        }
    }
}
