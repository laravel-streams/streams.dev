<?php

namespace Tests\Feature;

use App\Support\DocsNav;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Http;
use Streams\Core\Support\Facades\Streams;
use Symfony\Component\Yaml\Yaml;
use Tests\TestCase;

/**
 * Lints hub guides and package reference against the frontmatter schema
 * in STYLE.md. Agents copy these pages verbatim, so they must stay
 * machine-readable.
 */
class DocsContentTest extends TestCase
{
    protected const SECTIONS = ['get-started', 'guides', 'concepts', 'reference', 'packages', 'contributing'];

    protected const PACKAGES = ['core', 'ui', 'api', 'sdk', 'testing', 'client', 'site', 'all'];

    protected const STATUSES = ['implemented', 'partial', 'planned'];

    /**
     * @return array<string, array{frontmatter: array, body: string}>
     */
    protected function pages(): array
    {
        $pages = [];
        $files = glob(base_path('streams/data/docs/*.md')) ?: [];

        foreach (DocsNav::packages() as $package => $ignored) {
            $files = array_merge($files, glob(DocsNav::directory($package).'/*.md') ?: []);
        }

        foreach ($files as $file) {
            $text = file_get_contents($file);

            $this->assertMatchesRegularExpression('/^---\n.*?\n---\n/s', $text, "{$file} has no frontmatter.");

            preg_match('/^---\n(.*?)\n---\n/s', $text, $match);

            $relative = ltrim(str_replace(base_path(), '', $file), '/');

            $pages[substr($relative, 0, -3)] = [
                'frontmatter' => Yaml::parse($match[1]),
                'body' => substr($text, strlen($match[0])),
            ];
        }

        $this->assertNotEmpty($pages);

        return $pages;
    }

    public function test_frontmatter_matches_the_schema()
    {
        $categories = array_keys(json_decode(file_get_contents(base_path('streams/docs_categories.json')), true)['data']);

        foreach ($this->pages() as $page => ['frontmatter' => $frontmatter]) {
            foreach (['title', 'nav_title', 'description', 'section', 'package', 'order', 'tags', 'status'] as $key) {
                $this->assertArrayHasKey($key, $frontmatter, "{$page} is missing \"{$key}\".");
            }

            $this->assertIsString($frontmatter['title'], $page);
            $this->assertNotSame('', trim($frontmatter['description']), "{$page} has an empty description.");
            $this->assertContains($frontmatter['section'], static::SECTIONS, "{$page} section");
            $this->assertContains($frontmatter['package'], static::PACKAGES, "{$page} package");
            $this->assertContains($frontmatter['status'], static::STATUSES, "{$page} status");
            if (in_array($frontmatter['status'], ['implemented', 'partial', 'planned'], true)) {
                $this->assertArrayHasKey('tracking_task', $frontmatter, "{$page} tracking_task");
                if ($frontmatter['status'] !== 'implemented') {
                    $tracking = $frontmatter['tracking_task'];
                    $this->assertTrue($tracking === 'pending' || (is_int($tracking) && $tracking > 0), "{$page} needs a tracking task id or pending placeholder.");
                }
            }
            $this->assertIsInt($frontmatter['order'], "{$page} order");
            $this->assertIsArray($frontmatter['tags'], "{$page} tags");
            $this->assertArrayNotHasKey('sort_order', $frontmatter, "{$page} uses sort_order; use order.");

            if (str_starts_with($page, 'streams/data/docs/')) {
                $this->assertContains($frontmatter['category'] ?? null, $categories, "{$page} category");
            } else {
                $this->assertArrayNotHasKey('category', $frontmatter, "{$page}: package pages have no category.");
                $this->assertArrayNotHasKey('group', $frontmatter, "{$page}: package headings live in docs/nav.json.");
                $this->assertSame('packages', $frontmatter['section'], "{$page} section");
            }
        }
    }

    public function test_titles_are_unique()
    {
        $titles = array_map(fn ($page) => $page['frontmatter']['title'], $this->pages());

        $duplicates = array_keys(array_filter(array_count_values($titles), fn ($count) => $count > 1));

        $this->assertSame([], $duplicates, 'Duplicate titles: '.implode(', ', $duplicates));
    }

    public function test_bodies_have_no_h1_fenced_code_has_a_language_and_links_are_root_relative()
    {
        foreach ($this->pages() as $page => ['body' => $body]) {
            $inFence = false;

            foreach (explode("\n", $body) as $number => $line) {
                $where = "{$page} body line ".($number + 1);

                if (preg_match('/^\s*(```|~~~)(.*)$/', $line, $fence)) {
                    if (! $inFence) {
                        $this->assertNotSame('', trim($fence[2]), "{$where}: give the code fence a language (text for plain output).");
                    }

                    $inFence = ! $inFence;

                    continue;
                }

                if ($inFence) {
                    continue;
                }

                $this->assertDoesNotMatchRegularExpression('/^# /', $line, "{$where}: the layout renders the title as the H1.");

                preg_match_all('/\]\(([^)\s]+)/', $line, $links);

                foreach ($links[1] as $url) {
                    $this->assertMatchesRegularExpression('~^(https?:|/|#|mailto:)~', $url, "{$where}: use a root-relative link, not [{$url}].");
                }
            }

            $this->assertFalse($inFence, "{$page} has an unclosed code fence.");
        }
    }

    public function test_every_json_block_parses_and_uses_registered_field_types()
    {
        $count = 0;

        foreach ($this->pages() as $page => ['body' => $body]) {
            preg_match_all('/^```json[^\n]*\n(.*?)^```/ms', $body, $blocks);

            foreach ($blocks[1] as $index => $json) {
                $count++;
                $where = "{$page} json block ".($index + 1);

                $decoded = json_decode($json, true);

                $this->assertSame(JSON_ERROR_NONE, json_last_error(), "{$where}: ".json_last_error_msg()."\n{$json}");

                foreach ($this->fieldTypes($decoded) as $type) {
                    $this->assertTrue(App::has("streams.core.field_type.{$type}"), "{$where}: field type [{$type}] is not registered by Core.");
                }
            }
        }

        $this->assertGreaterThan(100, $count);
    }

    /**
     * Field types used in any "fields" definition inside a decoded block. A
     * list of strings is output (streams:list), not a definition, so it is skipped.
     */
    protected function fieldTypes(mixed $value): array
    {
        if (! is_array($value)) {
            return [];
        }

        $types = [];

        foreach ($value as $key => $child) {
            if ($key === 'fields' && is_array($child)) {
                foreach ($child as $field) {
                    $type = is_array($field) ? ($field['type'] ?? null) : (array_is_list($child) ? null : $field);

                    if (is_string($type) && ! str_starts_with($type, '@')) {
                        $types[] = $type;
                    }
                }
            }

            $types = array_merge($types, $this->fieldTypes($child));
        }

        return $types;
    }

    public function test_package_catalog_data_is_an_object_keyed_by_id(): void
    {
        $page = $this->pages()['streams/data/docs/package-catalog'];
        $body = $page['body'];

        $this->assertStringContainsString('object keyed by entry id', $body);
        $this->assertStringNotContainsString('the `data` array', $body);
        $this->assertSame('implemented', $page['frontmatter']['status']);
        $this->assertSame(267, $page['frontmatter']['tracking_task']);

        $packages = json_decode(file_get_contents(base_path('streams/packages.json')), true, 512, JSON_THROW_ON_ERROR);

        $this->assertSame('self', $packages['config']['source']['type']);
        $this->assertIsArray($packages['data']);
        $this->assertNotEmpty($packages['data']);
        $this->assertFalse(array_is_list($packages['data']), 'packages.json data is a list; self source stores an object keyed by id.');

        foreach ($packages['data'] as $key => $entry) {
            $this->assertSame($key, $entry['id']);
        }

        $this->get('/docs/package-catalog')
            ->assertOk()
            ->assertDontSee('data-docs-status="partial"', false)
            ->assertDontSee('the `data` array', false)
            ->assertSee('object keyed by entry id', false);
    }

    public function test_project_structure_composer_json_uses_vcs_repositories(): void
    {
        $page = $this->pages()['streams/data/docs/project-structure'];
        $body = $page['body'];

        $this->assertMatchesRegularExpression('/composer\.json\s+# Core, UI, vcs GitHub repositories/', $body);
        $this->assertDoesNotMatchRegularExpression('/composer\.json\s+#.*path repos/', $body);
        $this->assertSame('implemented', $page['frontmatter']['status']);
        $this->assertSame(267, $page['frontmatter']['tracking_task']);
        $this->assertStringContainsString('composer.local.json', $body);
        $this->assertStringContainsString('symlinked path repositories', $body);

        $composer = json_decode(file_get_contents(base_path('composer.json')), true, 512, JSON_THROW_ON_ERROR);

        $this->assertNotEmpty($composer['repositories']);

        foreach ($composer['repositories'] as $repository) {
            $this->assertSame('vcs', $repository['type']);
            $this->assertStringContainsString('github.com', $repository['url']);
        }

        $this->assertStringContainsString('/composer.local.json', file_get_contents(base_path('.gitignore')));

        $script = file_get_contents(base_path('scripts/composer-local.php'));
        $this->assertStringContainsString("'type' => 'path'", $script);
        $this->assertStringContainsString("'symlink' => true", $script);
        $this->assertStringContainsString('composer.local.json', $script);

        $provider = file_get_contents(base_path('app/Providers/AppServiceProvider.php'));
        $this->assertStringContainsString("Panel::make('admin')", $provider);
        $this->assertStringContainsString("->path('admin')", $provider);

        $this->get('/docs/project-structure')
            ->assertOk()
            ->assertDontSee('data-docs-status="partial"', false)
            ->assertDontSee('Core, UI, path repos', false)
            ->assertSee('vcs GitHub repositories', false);
    }

    public function test_site_pages_homepage_uses_layout_page(): void
    {
        $page = $this->pages()['streams/data/docs/site-pages'];
        $body = $page['body'];

        $this->assertStringContainsString('The homepage (`welcome.html`) uses `uri: /` and `layout: page`.', $body);
        $this->assertStringNotContainsString('The homepage (`welcome.html`) uses `uri: /` and `layout: blank`.', $body);
        $this->assertSame('implemented', $page['frontmatter']['status']);
        $this->assertSame(267, $page['frontmatter']['tracking_task']);
        $this->assertStringContainsString('`parse: true`', $body);
        $this->assertStringContainsString('"{uri}"', $body);

        preg_match('/^---\n(.*?)\n---\n/s', file_get_contents(base_path('streams/data/pages/welcome.html')), $welcome);
        $welcomeFrontmatter = Yaml::parse($welcome[1]);

        $this->assertSame('/', $welcomeFrontmatter['uri']);
        $this->assertSame('page', $welcomeFrontmatter['layout']);

        $pages = json_decode(file_get_contents(base_path('streams/pages.json')), true, 512, JSON_THROW_ON_ERROR);
        $route = $pages['routes'][0];

        $this->assertSame('view', $route['handle']);
        $this->assertSame('{uri}', $route['uri']);
        $this->assertTrue($route['parse']);
        $this->assertSame('{layout}', $route['view']);

        $this->get('/docs/site-pages')
            ->assertOk()
            ->assertDontSee('data-docs-status="partial"', false)
            ->assertDontSee('layout: blank', false)
            ->assertSee('layout: page', false);
    }

    public function test_local_development_uses_master_and_the_dev_ports(): void
    {
        $page = $this->pages()['streams/data/docs/local-development'];
        $body = $page['body'];

        $this->assertSame('implemented', $page['frontmatter']['status']);
        $this->assertSame(191, $page['frontmatter']['tracking_task']);

        $this->assertStringContainsString(
            'git clone https://github.com/laravel-streams/streams.dev.git --branch master',
            $body
        );
        $this->assertStringNotContainsString('--branch develop', $body);
        $this->assertStringNotContainsString('branch `develop`', $body);
        $this->assertStringNotContainsString('Work on `develop`', $body);
        $this->assertStringContainsString('branch `master`', $body);
        $this->assertStringContainsString('http://127.0.0.1:8427', $body);
        $this->assertStringContainsString('port 5427', $body);
        $this->assertStringContainsString('git switch -c feature/your-change', $body);
        $this->assertStringContainsString('The old `develop`, `next`, `integration/rc` and `production` branches no longer exist.', $body);

        $blade = file_get_contents(base_path('resources/views/docs.blade.php'));
        $this->assertStringContainsString('https://github.com/laravel-streams/streams.dev/blob/master/', $blade);
        $this->assertStringNotContainsString('/blob/develop/', $blade);

        $this->get('/docs/local-development')
            ->assertOk()
            ->assertDontSee('data-docs-status="partial"', false)
            ->assertDontSee('--branch develop', false)
            ->assertSee('--branch master', false)
            ->assertSee('127.0.0.1:8427', false)
            ->assertSee('5427', false);
    }

    public function test_changelog_release_candidates_require_php_82_on_rc_prep(): void
    {
        $page = $this->pages()['streams/data/docs/changelog'];
        $body = $page['body'];

        $this->assertStringContainsString(
            'On `rc/prep`, Core and API require PHP 8.2 (`php: ^8.2`).',
            $body
        );
        $this->assertStringNotContainsString(
            '**Core, API (release candidates):** Require PHP 8.2.',
            $body
        );
        $this->assertStringContainsString('The SDK now requires PHP 8.2.', $body);
        $this->assertStringContainsString('Core supports the Laravel 11 and 12 filesystem contract and Carbon 3.', $body);
        $this->assertSame('implemented', $page['frontmatter']['status']);
        $this->assertSame(268, $page['frontmatter']['tracking_task']);

        $core = json_decode(file_get_contents(base_path('vendor/streams/core/composer.json')), true, 512, JSON_THROW_ON_ERROR);
        $sdk = json_decode(file_get_contents(base_path('vendor/streams/sdk/composer.json')), true, 512, JSON_THROW_ON_ERROR);

        $this->assertSame('^8.2', $core['require']['php'] ?? null);
        $this->assertSame('^8.2', $sdk['require']['php'] ?? null);

        $registry = json_decode(file_get_contents(base_path('docs/registry.json')), true, 512, JSON_THROW_ON_ERROR);
        $changelogProof = [];
        foreach ($registry as $row) {
            if ($row['page'] === 'streams/data/docs/changelog.md') {
                $changelogProof = $row['proof'];
            }
        }
        $apiCommit = '0d3ba86d22473114553779420d3c1e15bb096f20';
        $this->assertContains('streams-api@'.$apiCommit.':composer.json', $changelogProof);

        // streams/api is not installed in this app. Read the rc/prep snapshot the registry cites.
        $api = $this->registryPackageComposer('streams-api', $apiCommit, 'rc/prep');
        $this->assertSame('streams/api', $api['name']);
        $this->assertArrayHasKey('php', $api['require']);
        $this->assertSame('^8.2', $api['require']['php']);

        $this->assertStringContainsString(
            'function registerMcpServer(',
            file_get_contents(base_path('vendor/streams/sdk/src/SdkServiceProvider.php'))
        );

        $lock = json_decode(file_get_contents(base_path('composer.lock')), true, 512, JSON_THROW_ON_ERROR);
        $lockedCore = null;

        foreach ($lock['packages'] as $package) {
            if ($package['name'] === 'streams/core') {
                $lockedCore = $package;
            }
        }

        $this->assertNotNull($lockedCore);
        $this->assertSame('9291456367b85482f83504046a37ec6f7dd59dd9', $lockedCore['source']['reference']);
        $this->assertSame('^8.2', $lockedCore['require']['php']);

        $this->get('/docs/changelog.md')
            ->assertOk()
            ->assertHeader('Content-Type', 'text/markdown; charset=UTF-8')
            ->assertSee('On `rc/prep`, Core and API require PHP 8.2 (`php: ^8.2`).', false)
            ->assertDontSee('**Core, API (release candidates):** Require PHP 8.2.', false);

        $this->get('/docs/changelog')
            ->assertOk()
            ->assertDontSee('data-docs-status="partial"', false)
            ->assertDontSee('data-docs-status="planned"', false)
            ->assertDontSee('This feature is partially implemented.', false);
    }

    public function test_use_cases_starter_requires_core_ui_and_api(): void
    {
        $page = $this->pages()['streams/data/docs/use-cases'];
        $body = $page['body'];

        $this->assertStringContainsString(
            '**Packages:** full starter via `composer create-project` (Core, UI, and API)',
            $body
        );
        $this->assertStringNotContainsString('(Core, UI, API, SDK)', $body);
        $this->assertStringContainsString('It does not require `streams/sdk`.', $body);
        $this->assertStringContainsString('**Packages:** `streams/core`, `streams/ui`', $body);
        $this->assertStringContainsString('**Packages:** `streams/core`, `streams/ui`, `streams/api`', $body);
        $this->assertStringContainsString('**Packages:** `streams/core`, `streams/api`', $body);
        $this->assertSame('implemented', $page['frontmatter']['status']);
        $this->assertSame(268, $page['frontmatter']['tracking_task']);

        $this->get('/docs/use-cases.md')
            ->assertOk()
            ->assertHeader('Content-Type', 'text/markdown; charset=UTF-8')
            ->assertSee('**Packages:** full starter via `composer create-project` (Core, UI, and API)', false)
            ->assertDontSee('(Core, UI, API, SDK)', false)
            ->assertSee('It does not require `streams/sdk`.', false);

        $this->get('/docs/use-cases')
            ->assertOk()
            ->assertDontSee('data-docs-status="partial"', false)
            ->assertDontSee('data-docs-status="planned"', false)
            ->assertDontSee('This feature is partially implemented.', false);
    }

    public function test_versions_page_matches_package_constraints(): void
    {
        $page = $this->pages()['streams/data/docs/versions'];
        $body = $page['body'];

        $this->assertSame('implemented', $page['frontmatter']['status']);
        $this->assertSame(270, $page['frontmatter']['tracking_task']);

        $this->assertStringContainsString('Branch-alias is `dev-master` => `2.x-dev`.', $body);
        $this->assertStringContainsString('none on `2.0`', $body);
        $this->assertStringContainsString('none on `1.0`', $body);
        $this->assertStringContainsString('On `rc/prep`, Core and API declare `php: ^8.2`.', $body);
        $this->assertStringContainsString('The SDK on `sdk/rc` declares `php: ^8.2`.', $body);
        $this->assertStringContainsString('Requires Livewire `^3.0`.', $body);
        $this->assertStringContainsString('`orchestra/testbench ^8.36`', $body);
        $this->assertStringContainsString('`^8.0.2`', $body);
        $this->assertStringContainsString('`^10.0`', $body);
        $this->assertStringContainsString('`^10\|^11\|^12`', $body);
        $this->assertStringContainsString('latest `v1.10.5`', $body);
        $this->assertStringContainsString('**Core 1.10.5**', $body);

        $this->assertStringNotContainsString('Core, API, and SDK declare PHP `^8.2`', $body);
        $this->assertStringNotContainsString('1.10.4', $body);
        $this->assertStringNotContainsString('| `^8.2` | Stable tags exist only', $body);
        $this->assertStringNotContainsString('| via Core | `^8.2` |', $body);

        $core20 = $this->registryPackageComposer('streams-core', '5fd458f43618f9e744f7c43646242491394508fc', '2.0');
        $coreRc = $this->registryPackageComposer('streams-core', '9291456367b85482f83504046a37ec6f7dd59dd9', 'rc/prep');
        $api10 = $this->registryPackageComposer('streams-api', '22d2351345631480102aa1377e6a6a73d765c7a5', '1.0');
        $apiRc = $this->registryPackageComposer('streams-api', '0d3ba86d22473114553779420d3c1e15bb096f20', 'rc/prep');
        $ui = $this->registryPackageComposer('streams-ui', '3a30c8676df7815804a31cf5388b8fee80cdb541', '1.0');
        $sdk10 = $this->registryPackageComposer('streams-sdk', '198c74c32e52b7abe0d99e4f74ee13296eee4d63', '1.0');
        $sdkRc = $this->registryPackageComposer('streams-sdk', '2aff9ebe3a0a58b4e48cb244af0ee683e17ef7b9', 'sdk/rc');
        $testing = $this->registryPackageComposer('streams-testing', 'ec5dd5f41b814a2b15b5dc1aa8a9f5911281781c', '1.0');
        $starter = $this->registryPackageComposer('streams', '06fde618b8f8f8a12680f9d98314728de34c03dd', '1.0');

        $this->assertSame(['dev-master' => '2.x-dev'], $core20['extra']['branch-alias']);
        $this->assertArrayNotHasKey('php', $core20['require']);
        $this->assertSame('^10|^11|^12', $core20['require']['laravel/framework']);
        $this->assertSame('^8.2', $coreRc['require']['php']);
        $this->assertSame('^10|^11|^12', $coreRc['require']['laravel/framework']);
        $this->assertArrayNotHasKey('branch-alias', $coreRc['extra'] ?? []);
        $this->assertArrayNotHasKey('php', $api10['require']);
        $this->assertSame('^8.2', $apiRc['require']['php']);
        $this->assertSame('^3.0', $ui['require']['livewire/livewire']);
        $this->assertArrayNotHasKey('php', $sdk10['require']);
        $this->assertSame('^8.2', $sdkRc['require']['php']);
        $this->assertSame('^8.36', $testing['require']['orchestra/testbench']);
        $this->assertSame('^8.0.2', $starter['require']['php']);
        $this->assertSame('^10.0', $starter['require']['laravel/framework']);
        $this->assertSame('^2.0', $starter['require']['streams/core']);
        $this->assertSame('^1.0', $starter['require']['streams/ui']);
        $this->assertSame('^1.0', $starter['require']['streams/api']);

        $site = json_decode(file_get_contents(base_path('composer.json')), true, 512, JSON_THROW_ON_ERROR);
        $this->assertSame('dev-rc/prep as 2.0.x-dev', $site['require']['streams/core']);
        $this->assertSame('1.0.x-dev', $site['require']['streams/ui']);
        $this->assertSame('dev-sdk/rc as 1.0.x-dev', $site['require-dev']['streams/sdk']);

        $locked = [];
        $lock = json_decode(file_get_contents(base_path('composer.lock')), true, 512, JSON_THROW_ON_ERROR);

        foreach (array_merge($lock['packages'], $lock['packages-dev']) as $package) {
            $locked[$package['name']] = $package;
        }

        $this->assertSame('9291456367b85482f83504046a37ec6f7dd59dd9', $locked['streams/core']['source']['reference']);
        $this->assertSame('^8.2', $locked['streams/core']['require']['php']);
        $this->assertSame('^10|^11|^12', $locked['streams/core']['require']['laravel/framework']);
        $this->assertSame('^3.0', $locked['streams/ui']['require']['livewire/livewire']);
        $this->assertSame('^8.2', $locked['streams/sdk']['require']['php']);

        $latest = Http::timeout(20)
            ->withHeaders(['User-Agent' => 'streams.dev-docs-test'])
            ->get('https://repo.packagist.org/p2/streams/core.json')
            ->throw()
            ->json('packages.streams/core.0.version');

        $this->assertSame('v1.10.5', $latest);

        $row = null;
        $registry = json_decode(file_get_contents(base_path('docs/registry.json')), true, 512, JSON_THROW_ON_ERROR);

        foreach ($registry as $candidate) {
            if ($candidate['page'] === 'streams/data/docs/versions.md') {
                $row = $candidate;
            }
        }

        $this->assertNotNull($row);
        $this->assertSame('implemented', $row['status']);
        $this->assertContains('streams-core@5fd458f43618f9e744f7c43646242491394508fc:composer.json', $row['proof']);
        $this->assertContains('streams-core@9291456367b85482f83504046a37ec6f7dd59dd9:composer.json', $row['proof']);
        $this->assertContains('streams-ui@3a30c8676df7815804a31cf5388b8fee80cdb541:composer.json', $row['proof']);
        $this->assertContains('streams-api@22d2351345631480102aa1377e6a6a73d765c7a5:composer.json', $row['proof']);
        $this->assertContains('streams-api@0d3ba86d22473114553779420d3c1e15bb096f20:composer.json', $row['proof']);
        $this->assertContains('streams-sdk@198c74c32e52b7abe0d99e4f74ee13296eee4d63:composer.json', $row['proof']);
        $this->assertContains('streams-sdk@2aff9ebe3a0a58b4e48cb244af0ee683e17ef7b9:composer.json', $row['proof']);
        $this->assertContains('streams-testing@ec5dd5f41b814a2b15b5dc1aa8a9f5911281781c:composer.json', $row['proof']);
        $this->assertContains('streams@06fde618b8f8f8a12680f9d98314728de34c03dd:composer.json', $row['proof']);

        $this->get('/docs/versions.md')
            ->assertOk()
            ->assertHeader('Content-Type', 'text/markdown; charset=UTF-8')
            ->assertSee('Branch-alias is `dev-master` => `2.x-dev`.', false)
            ->assertSee('On `rc/prep`, Core and API declare `php: ^8.2`.', false)
            ->assertDontSee('Core, API, and SDK declare PHP `^8.2`', false)
            ->assertDontSee('1.10.4', false);

        $this->get('/docs/versions')
            ->assertOk()
            ->assertDontSee('data-docs-status="partial"', false)
            ->assertDontSee('data-docs-status="planned"', false)
            ->assertDontSee('This feature is partially implemented.', false)
            ->assertSee('dev-master', false)
            ->assertSee('2.x-dev', false)
            ->assertSee('v1.10.5', false)
            ->assertDontSee('1.10.4', false)
            ->assertDontSee('Core, API, and SDK declare PHP', false);
    }

    public function test_package_nav_points_at_package_files()
    {
        foreach (DocsNav::packages() as $name => $package) {
            $this->assertSame(
                'docs/packages/'.$name,
                Streams::make($package['stream'])->config('source.path'),
                "{$name} stream does not point at its package folder."
            );

            $listed = DocsNav::listed($name);

            $this->assertSame($listed, array_values(array_unique($listed)), "{$name} lists a page more than once.");

            foreach ($listed as $id) {
                $this->assertFileExists(DocsNav::directory($name).'/'.$id.'.md', "{$name} nav points at missing {$id}.md.");
            }

            $this->assertEqualsCanonicalizing(
                DocsNav::files($name),
                array_merge($listed, DocsNav::unlisted($name)),
                "{$name} nav does not account for every markdown file."
            );
        }
    }

    /**
     * Composer manifest at a registry proof commit.
     *
     * Prefers a local checkout (the Core symlink's sibling, then the paths
     * scripts/composer-local.php searches). Otherwise reads that commit from GitHub.
     *
     * @return array<string, mixed>
     */
    protected function registryPackageComposer(string $repository, string $commit, string $branch): array
    {
        foreach ($this->packageCheckouts($repository) as $checkout) {
            if (! $this->gitSucceeds($checkout, ['cat-file', '-e', $commit])) {
                continue;
            }

            $this->assertTrue(
                $this->gitSucceeds($checkout, ['merge-base', '--is-ancestor', $commit, $branch]),
                "{$repository}@{$commit} is not on {$branch}."
            );

            return json_decode(
                $this->gitOutput($checkout, ['show', $commit.':composer.json']),
                true,
                512,
                JSON_THROW_ON_ERROR
            );
        }

        $headers = [
            'User-Agent' => 'streams.dev-docs-test',
            'Accept' => 'application/vnd.github+json',
        ];

        $composer = Http::timeout(20)
            ->withHeaders($headers)
            ->get("https://raw.githubusercontent.com/laravel-streams/{$repository}/{$commit}/composer.json")
            ->throw()
            ->json();

        $this->assertIsArray($composer, "{$repository}@{$commit} composer.json was not readable.");

        $status = Http::timeout(20)
            ->withHeaders($headers)
            ->get('https://api.github.com/repos/laravel-streams/'.$repository.'/compare/'.rawurlencode($branch).'...'.$commit)
            ->throw()
            ->json('status');

        $this->assertContains($status, ['identical', 'behind'], "{$repository}@{$commit} is not on {$branch}.");

        return $composer;
    }

    /**
     * @return list<string>
     */
    protected function packageCheckouts(string $repository): array
    {
        $package = str_starts_with($repository, 'streams-') ? substr($repository, strlen('streams-')) : $repository;
        $env = getenv('STREAMS_'.strtoupper($package).'_PATH') ?: '';
        $candidates = $env !== '' ? [$env] : [];

        $core = realpath(base_path('vendor/streams/core'));
        if ($core !== false) {
            $candidates[] = dirname($core).'/'.$repository;
        }

        foreach ([
            base_path('../_rc/'.$repository),
            base_path('../_packages/'.$repository),
            base_path('../'.$repository),
        ] as $candidate) {
            $candidates[] = $candidate;
        }

        $found = [];
        foreach ($candidates as $candidate) {
            $path = rtrim($candidate, '/');
            if (is_file($path.'/composer.json')) {
                $found[] = $path;
            }
        }

        return array_values(array_unique($found));
    }

    /**
     * @param  list<string>  $arguments
     */
    protected function gitSucceeds(string $checkout, array $arguments): bool
    {
        return $this->git($checkout, $arguments)['code'] === 0;
    }

    /**
     * @param  list<string>  $arguments
     */
    protected function gitOutput(string $checkout, array $arguments): string
    {
        $result = $this->git($checkout, $arguments);
        $this->assertSame(0, $result['code'], "git failed in {$checkout}: ".$result['output']);

        return $result['output'];
    }

    /**
     * @param  list<string>  $arguments
     * @return array{code: int, output: string}
     */
    protected function git(string $checkout, array $arguments): array
    {
        $command = 'git -C '.escapeshellarg($checkout);
        foreach ($arguments as $argument) {
            $command .= ' '.escapeshellarg($argument);
        }

        exec($command.' 2>&1', $output, $code);

        return ['code' => $code, 'output' => trim(implode("\n", $output))];
    }
}
