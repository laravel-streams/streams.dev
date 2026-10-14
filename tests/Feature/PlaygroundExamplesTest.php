<?php

namespace Tests\Feature;

use App\Support\PlaygroundExamples;
use InvalidArgumentException;
use Streams\Core\Support\Facades\Streams;
use Tests\TestCase;

class PlaygroundExamplesTest extends TestCase
{
    public function test_every_builder_type_has_an_example_that_renders(): void
    {
        $types = PlaygroundExamples::builderTypes();
        $files = glob(resource_path('playground/examples/*.json')) ?: [];
        $fileTypes = array_map(
            fn (string $file): string => basename($file, '.json'),
            $files
        );

        $this->assertNotEmpty($types);
        $this->assertSame($types, array_values(array_unique($types)));
        $this->assertEqualsCanonicalizing($types, $fileTypes);

        $installed = PlaygroundExamples::installedCatalogTypes();

        if ($installed !== null) {
            $this->assertSame($installed, $types);
        }

        $before = $this->seedCounts();
        $seedFiles = $this->seedFileHashes();

        foreach (PlaygroundExamples::all() as $type => $example) {
            $html = PlaygroundExamples::render($example);

            $this->assertNotSame('', trim($html), $type);
            $this->assertStringContainsString('data-builder="'.$type.'"', $html, $type);
            $this->assertStringContainsString($example['label'], $html, $type);
        }

        $this->assertSame([6, 5, 8], $before);
        $this->assertSame($before, $this->seedCounts());
        $this->assertSame($seedFiles, $this->seedFileHashes());
    }

    public function test_composite_previews_render_content_instead_of_component_names(): void
    {
        $table = PlaygroundExamples::render(PlaygroundExamples::get('livewire-table'));
        $form = PlaygroundExamples::render(PlaygroundExamples::get('livewire-form'));
        $page = PlaygroundExamples::render(PlaygroundExamples::get('livewire-page'));
        $widget = PlaygroundExamples::render(PlaygroundExamples::get('livewire-widget'));

        $this->assertStringContainsString('<table>', $table);
        $this->assertStringContainsString('Ada Rivera', $table);
        $this->assertStringContainsString('Welcome to the playground', $form);
        $this->assertStringContainsString('<textarea', $form);
        $this->assertStringContainsString('PG-1001', $page);
        $this->assertStringContainsString('<output aria-label="Open orders">5</output>', $widget);

        $modal = PlaygroundExamples::render(PlaygroundExamples::get('modal'));
        $prompt = PlaygroundExamples::render(PlaygroundExamples::get('prompt'));
        $tab = PlaygroundExamples::render(PlaygroundExamples::get('tab'));

        $this->assertStringContainsString('role="dialog"', $modal);
        $this->assertStringContainsString('Cancel order PG-1001?', $modal);
        $this->assertStringContainsString('<legend>Cancel order PG-1001?</legend>', $prompt);
        $this->assertStringContainsString('role="tab" aria-selected="true"', $tab);

        $resource = PlaygroundExamples::get('resource');
        $stream = Streams::make($resource['stream']);

        foreach (array_merge($resource['config']['table_columns'], $resource['config']['form_fields']) as $handle) {
            $this->assertNotNull($stream->fields->get($handle), $handle);
        }

        $resourceHtml = PlaygroundExamples::render($resource);
        $this->assertStringContainsString('number, status, total_cents', $resourceHtml);
        $this->assertStringContainsString('customer, status', $resourceHtml);
    }

    public function test_example_labels_and_values_are_escaped(): void
    {
        $example = PlaygroundExamples::get('input-text');
        $example['label'] = '<script>alert(1)</script>';
        $example['config']['value'] = '\"><script>alert(2)</script>';
        unset($example['entry']);

        $html = PlaygroundExamples::render($example);

        $this->assertStringNotContainsString('<script>', $html);
        $this->assertStringContainsString('&lt;script&gt;', $html);
    }

    public function test_unknown_builder_cannot_load_a_file_outside_the_examples_directory(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unknown playground builder');

        PlaygroundExamples::get('../builder-types');
    }

    public function test_bound_examples_require_a_seeded_stream(): void
    {
        $example = PlaygroundExamples::get('form');
        unset($example['stream']);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('must name a seeded stream');

        PlaygroundExamples::render($example);
    }

    /**
     * @return array<string, string>
     */
    private function seedFileHashes(): array
    {
        $hashes = [];

        foreach (glob(base_path('streams/data/playground/*/*.json')) ?: [] as $path) {
            $hashes[$path] = (string) hash_file('sha256', $path);
        }

        return $hashes;
    }

    public function test_table_form_and_action_examples_use_the_seeded_streams(): void
    {
        $table = PlaygroundExamples::get('table');
        $form = PlaygroundExamples::get('form');
        $action = PlaygroundExamples::get('action');

        $this->assertEqualsCanonicalizing(
            ['people', 'posts', 'orders'],
            [$table['stream'], $form['stream'], $action['stream']]
        );

        $tableHtml = PlaygroundExamples::render($table);
        $formHtml = PlaygroundExamples::render($form);
        $actionHtml = PlaygroundExamples::render($action);

        $this->assertStringContainsString('data-stream="people"', $tableHtml);
        $this->assertSame(6, substr_count($tableHtml, 'data-entry="'));
        $this->assertStringContainsString('Ada Rivera', $tableHtml);
        $this->assertStringContainsString('ada@example.test', $tableHtml);

        $this->assertStringContainsString('data-stream="posts"', $formHtml);
        $this->assertStringContainsString('Welcome to the playground', $formHtml);
        $this->assertStringContainsString('Ada Rivera', $formHtml);
        $this->assertStringNotContainsString('type="submit"', $formHtml);

        $this->assertStringContainsString('data-stream="orders"', $actionHtml);
        $this->assertStringContainsString('PG-1001', $actionHtml);
        $this->assertStringContainsString('type="button"', $actionHtml);
        $this->assertStringNotContainsString('type="submit"', $actionHtml);

        $email = PlaygroundExamples::render(PlaygroundExamples::get('input-email'));
        $this->assertStringContainsString('value="ada@example.test"', $email);
        $this->assertStringContainsString('data-entry="person-ada"', $tableHtml);

        $checkbox = PlaygroundExamples::render(PlaygroundExamples::get('input-checkbox'));
        $toggle = PlaygroundExamples::render(PlaygroundExamples::get('input-toggle'));
        $this->assertMatchesRegularExpression('/type="checkbox"[^>]*checked/', $checkbox);
        $this->assertDoesNotMatchRegularExpression('/type="checkbox"[^>]*checked/', $toggle);
    }

    /**
     * @return array{0: int, 1: int, 2: int}
     */
    private function seedCounts(): array
    {
        return [
            Streams::entries('people')->count(),
            Streams::entries('posts')->count(),
            Streams::entries('orders')->count(),
        ];
    }
}
