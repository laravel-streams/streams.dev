<?php

namespace Tests\Feature;

use Symfony\Component\Process\Process;
use Tests\TestCase;

class DocsSyncTest extends TestCase
{
    /** @var list<string> */
    protected array $fixtures = [];

    protected function tearDown(): void
    {
        putenv('STREAMS_DOCS_ROOT');
        putenv('STREAMS_CORE_PATH');
        unset($_ENV['STREAMS_DOCS_ROOT'], $_ENV['STREAMS_CORE_PATH']);

        foreach ($this->fixtures as $path) {
            $this->removeTree($path);
        }

        parent::tearDown();
    }

    public function test_push_mirrors_markdown_and_deletes_stale_pages()
    {
        $root = $this->fixture('2.0');

        $this->artisan('docs:sync', ['direction' => 'push', 'package' => 'core', '--dry-run' => true])
            ->expectsOutputToContain('copy introduction.md')
            ->expectsOutputToContain('delete helpers.md')
            ->assertOk();

        $this->assertSame("old intro\n", file_get_contents($root.'/repo/docs/introduction.md'));
        $this->assertFileExists($root.'/repo/docs/helpers.md');

        $this->artisan('docs:sync', ['direction' => 'push', 'package' => 'core'])->assertOk();

        $this->assertStringContainsString('Site body', file_get_contents($root.'/repo/docs/introduction.md'));
        $this->assertFileExists($root.'/repo/docs/extra.md');
        $this->assertFileDoesNotExist($root.'/repo/docs/helpers.md');
    }

    public function test_pull_mirrors_the_package_onto_the_site_folder()
    {
        $root = $this->fixture('2.0');

        file_put_contents($root.'/repo/docs/introduction.md', "from repo\n");
        unlink($root.'/repo/docs/helpers.md');
        $this->commit($root.'/repo');

        $this->artisan('docs:sync', ['direction' => 'pull', 'package' => 'core'])->assertOk();

        $this->assertSame("from repo\n", file_get_contents($root.'/site/core/introduction.md'));
        $this->assertFileDoesNotExist($root.'/site/core/extra.md');
        $this->assertFileDoesNotExist($root.'/site/core/helpers.md');
    }

    public function test_page_option_copies_one_file_and_leaves_the_rest()
    {
        $root = $this->fixture('2.0');

        $this->artisan('docs:sync', [
            'direction' => 'push',
            'package' => 'core',
            '--page' => 'introduction',
        ])->assertOk();

        $this->assertStringContainsString('Site body', file_get_contents($root.'/repo/docs/introduction.md'));
        $this->assertFileExists($root.'/repo/docs/helpers.md');
        $this->assertFileDoesNotExist($root.'/repo/docs/extra.md');
    }

    public function test_a_checkout_on_the_wrong_branch_is_refused()
    {
        $root = $this->fixture('main');
        $before = file_get_contents($root.'/repo/docs/introduction.md');

        $this->artisan('docs:sync', ['direction' => 'push', 'package' => 'core'])
            ->expectsOutputToContain('is on main')
            ->assertFailed();

        $this->assertSame($before, file_get_contents($root.'/repo/docs/introduction.md'));
    }

    public function test_uncommitted_docs_are_left_alone()
    {
        $root = $this->fixture('2.0');
        file_put_contents($root.'/repo/docs/introduction.md', "dirty\n");

        $this->artisan('docs:sync', ['direction' => 'push', 'package' => 'core'])
            ->expectsOutputToContain('uncommitted changes')
            ->assertFailed();

        $this->assertSame("dirty\n", file_get_contents($root.'/repo/docs/introduction.md'));
    }

    public function test_push_flag_requires_commit()
    {
        $this->artisan('docs:sync', ['direction' => 'push', 'package' => 'core', '--push' => true])
            ->expectsOutputToContain('--push requires --commit')
            ->assertFailed();
    }

    public function test_hub_guides_are_not_part_of_the_sync()
    {
        $root = $this->fixture('2.0');
        $hub = md5_file(base_path('streams/data/docs/introduction.md'));
        $nav = md5_file(base_path('docs/nav.json'));

        $this->artisan('docs:sync', ['direction' => 'push', 'package' => 'core'])->assertOk();

        $this->assertSame($hub, md5_file(base_path('streams/data/docs/introduction.md')));
        $this->assertSame($nav, md5_file(base_path('docs/nav.json')));
        $this->assertFileDoesNotExist($root.'/repo/docs/nav.json');
        $this->assertDirectoryDoesNotExist($root.'/repo/streams');
    }

    protected function fixture(string $branch): string
    {
        $root = sys_get_temp_dir().'/streams-docs-sync-'.bin2hex(random_bytes(4));
        mkdir($root.'/site/core', 0777, true);
        mkdir($root.'/repo/docs', 0777, true);

        file_put_contents($root.'/site/core/introduction.md', "---\ntitle: Site\n---\n\nSite body\n");
        file_put_contents($root.'/site/core/extra.md', "site extra\n");
        file_put_contents($root.'/repo/docs/introduction.md', "old intro\n");
        file_put_contents($root.'/repo/docs/helpers.md', "stale\n");

        $this->gitInit($root.'/site', 'main');
        $this->gitInit($root.'/repo', $branch);

        putenv('STREAMS_DOCS_ROOT='.$root.'/site');
        putenv('STREAMS_CORE_PATH='.$root.'/repo');

        $this->fixtures[] = $root;

        return $root;
    }

    protected function gitInit(string $directory, string $branch): void
    {
        $this->git($directory, ['git', 'init', '-b', $branch]);
        $this->git($directory, ['git', 'config', 'user.email', 'docs-sync@example.com']);
        $this->git($directory, ['git', 'config', 'user.name', 'Docs Sync Test']);
        $this->commit($directory);
    }

    protected function commit(string $directory): void
    {
        $this->git($directory, ['git', 'add', '.']);
        $this->git($directory, ['git', 'commit', '-m', 'sync fixture']);
    }

    protected function git(string $directory, array $command): void
    {
        $process = new Process($command, $directory);
        $process->mustRun();
    }

    protected function removeTree(string $path): void
    {
        if (! is_dir($path)) {
            return;
        }

        $items = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($items as $item) {
            $item->isDir() ? rmdir($item->getPathname()) : unlink($item->getPathname());
        }

        rmdir($path);
    }
}
