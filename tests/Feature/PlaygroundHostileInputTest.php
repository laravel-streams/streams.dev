<?php

namespace Tests\Feature;

use App\Livewire\PlaygroundConfig;
use App\Support\PlaygroundCatalog;
use App\Support\PlaygroundPreview;
use App\Support\PlaygroundSchema;
use Illuminate\Support\Facades\File;
use Livewire\Exceptions\MethodNotFoundException;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PlaygroundHostileInputTest extends TestCase
{
    /** @return array<string, array{0: array<string, mixed>}> */
    public static function hostileNodes(): array
    {
        $text = 'Streams\\Ui\\Builders\\Inputs\\TextInput';
        $action = 'Streams\\Ui\\Builders\\Actions\\Action';

        return [
            'unknown key' => [['builder' => $text, 'defaults' => ['name' => 'title', 'writeFile' => 'owned']]],
            'class name' => [['builder' => $text, 'defaults' => ['name' => 'title', 'class' => 'owned']]],
            'foreign class' => [['builder' => 'Illuminate\\Filesystem\\Filesystem', 'defaults' => ['name' => 'title']]],
            'unknown UI class' => [['builder' => 'Streams\\Ui\\Builders\\Missing', 'defaults' => ['name' => 'title']]],
            'base builder class' => [['builder' => 'Streams\\Ui\\Builders\\ViewBuilder', 'defaults' => ['name' => 'title']]],
            'crafted link' => [['builder' => $action, 'defaults' => ['name' => 'edit', 'url' => 'javascript:alert(document.cookie)']]],
            'protocol relative link' => [['builder' => $action, 'defaults' => ['name' => 'edit', 'url' => '//evil.example/payload']]],
            'attribute injection' => [['builder' => $action, 'defaults' => ['name' => 'edit', 'url' => '/" onclick="alert(1)']]],
            'backslash link' => [['builder' => $action, 'defaults' => ['name' => 'edit', 'url' => '/\\evil.example']]],
            'control character link' => [['builder' => $action, 'defaults' => ['name' => 'edit', 'url' => "/\x00evil.example"]]],
            'action callback' => [['builder' => $action, 'defaults' => ['name' => 'delete', 'callback' => 'unlink']]],
            'missing nested defaults' => [[
                'builder' => 'Streams\\Ui\\Builders\\Forms\\Form',
                'defaults' => ['name' => 'contact', 'actions' => [
                    ['builder' => $action],
                ]],
            ]],
            'nested crafted link' => [[
                'builder' => 'Streams\\Ui\\Builders\\Forms\\Form',
                'defaults' => ['name' => 'contact', 'actions' => [
                    ['builder' => $action, 'defaults' => ['name' => 'delete', 'url' => 'data:text/html,<script>alert(1)</script>']],
                ]],
            ]],
        ];
    }

    /** @return array<string, array{0: string}> */
    public static function safeLinks(): array
    {
        return [
            'local' => ['/docs/ui/actions'],
            'fragment' => ['#preview'],
            'https' => ['https://streams.dev/docs/ui/actions'],
            'http' => ['http://localhost/docs/ui/actions'],
        ];
    }

    #[DataProvider('safeLinks')]
    public function test_safe_links_remain_editable(string $url): void
    {
        $node = PlaygroundCatalog::find('action');
        $node['defaults']['url'] = $url;
        $validated = PlaygroundSchema::validate(PlaygroundSchema::encodeNode($node));
        $this->assertTrue($validated['valid'], implode(' ', $validated['errors']));
        $rendered = PlaygroundPreview::render($node);
        $this->assertNull($rendered['gap']);
        $this->assertStringContainsString('href="'.$url.'"', $rendered['html']);
    }

    #[DataProvider('hostileNodes')]
    public function test_hostile_config_is_rejected_without_changing_preview_or_disk(array $node): void
    {
        $component = PlaygroundCatalog::find('text-input');
        $preview = PlaygroundPreview::render($component)['html'];
        $editor = Livewire::test(PlaygroundConfig::class, [
            'componentId' => 'text-input',
            'preview' => $preview,
        ]);

        // Compile the error state before observing disk, so compilation is not
        // mistaken for persistence by an attempted configuration update.
        $editor->set('config', '{')->assertSee('data-playground-errors', false);
        $before = $this->diskState();

        $json = json_encode($node, JSON_THROW_ON_ERROR);
        $validated = PlaygroundSchema::validate($json);
        $this->assertFalse($validated['valid']);
        $messages = implode("\n", $validated['errors']);

        if (isset($node['defaults']['writeFile'])) {
            $this->assertStringContainsString('/defaults/writeFile: Unknown keys are not allowed.', $messages);
        }

        if (isset($node['defaults']['class'])) {
            $this->assertStringContainsString('/defaults/class: Class names are not allowed.', $messages);
        }

        if (isset($node['defaults']['callback'])) {
            $this->assertStringContainsString('/defaults/callback: Action callbacks are not allowed.', $messages);
        }

        if (isset($node['defaults']['actions'][0]) && ! isset($node['defaults']['actions'][0]['defaults'])) {
            $this->assertStringContainsString('/defaults: The example config must include a defaults object.', $messages);
        }

        $editor->set('config', $json)
            ->assertSee('data-playground-errors', false)
            ->assertSet('preview', $preview)
            ->assertNotDispatched('playground-rendered');

        $this->assertSame($before, $this->diskState(), 'Hostile input changed files on disk.');
    }

    public function test_action_call_is_rejected_without_changing_disk(): void
    {
        $component = PlaygroundCatalog::find('action');
        $preview = PlaygroundPreview::render($component)['html'];
        $editor = Livewire::test(PlaygroundConfig::class, [
            'componentId' => 'action',
            'preview' => $preview,
        ]);
        $before = $this->diskState();

        try {
            // Package action directives must not gain access to an executable
            // action handler on the playground's configuration component.
            $editor->call('callAction', 'delete');
            $this->fail('The playground accepted an action call.');
        } catch (MethodNotFoundException $exception) {
            $this->assertStringContainsString('callAction', $exception->getMessage());
        }

        $this->assertSame($preview, $editor->instance()->preview);
        $this->assertSame($before, $this->diskState(), 'Action call changed files on disk.');
    }

    /** @return array<string, string> */
    private function diskState(): array
    {
        $state = [];

        // Include application code, flat-file entries, catalog/schema/assets,
        // and all storage (including caches), detecting added/deleted files too.
        foreach (['app', 'routes', 'streams', 'docs', 'resources', 'public', 'storage'] as $directory) {
            foreach (File::allFiles(base_path($directory)) as $file) {
                $state[$file->getPathname()] = hash_file('sha256', $file->getPathname());
            }
        }

        ksort($state);

        return $state;
    }
}
