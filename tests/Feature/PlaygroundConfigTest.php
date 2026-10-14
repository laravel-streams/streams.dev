<?php

namespace Tests\Feature;

use App\Livewire\PlaygroundConfig;
use App\Support\PlaygroundCatalog;
use App\Support\PlaygroundPreview;
use App\Support\PlaygroundSchema;
use Livewire\Livewire;
use Tests\TestCase;

class PlaygroundConfigTest extends TestCase
{
    public function test_catalog_nodes_match_catalog_schema_and_unknown_keys_are_rejected(): void
    {
        $schema = json_decode(file_get_contents(PlaygroundSchema::path()), true, 512, JSON_THROW_ON_ERROR);

        $this->assertSame(PlaygroundSchema::SCHEMA_ID, $schema['$id']);
        $this->assertFalse($schema['definitions']['node']['additionalProperties']);
        $this->assertFalse($schema['definitions']['defaults']['additionalProperties']);

        foreach (PlaygroundCatalog::components() as $component) {
            $validated = PlaygroundSchema::validate(PlaygroundSchema::encodeNode($component));

            $this->assertTrue($validated['valid'], $component['id'].' '.implode(' ', $validated['errors']));
            $this->assertSame($component['builder'], $validated['node']['builder']);
        }

        $node = json_decode(PlaygroundSchema::encodeNode(PlaygroundCatalog::find('text-input')), true, 512, JSON_THROW_ON_ERROR);
        $node['defaults']['notARealKey'] = 'nope';

        $invalid = PlaygroundSchema::validate(json_encode($node, JSON_THROW_ON_ERROR));

        $this->assertFalse($invalid['valid']);
        $this->assertStringContainsString('notARealKey', implode(' ', $invalid['errors']));

        $node['defaults'] = [
            'name' => 'contact',
            'components' => [[
                'builder' => 'Streams\\Ui\\Builders\\Inputs\\TextInput',
                'defaults' => ['name' => 'name', 'alsoUnknown' => 'x'],
            ]],
        ];
        $node['builder'] = 'Streams\\Ui\\Builders\\Forms\\Form';
        $nested = PlaygroundSchema::validate(json_encode($node, JSON_THROW_ON_ERROR));

        $this->assertFalse($nested['valid']);
        $this->assertStringContainsString('alsoUnknown', implode(' ', $nested['errors']));

        $broken = PlaygroundSchema::validate('{');

        $this->assertFalse($broken['valid']);
        $this->assertStringContainsString('Invalid JSON', $broken['errors'][0]);

        $typed = PlaygroundSchema::validate(json_encode([
            'builder' => 'Streams\\Ui\\Builders\\Inputs\\TextareaInput',
            'defaults' => ['name' => 'body', 'rows' => '4'],
        ], JSON_THROW_ON_ERROR));

        $this->assertFalse($typed['valid']);
        $this->assertStringContainsString('rows', implode(' ', $typed['errors']));
    }

    public function test_catalog_schema_is_published(): void
    {
        $this->get('/schema/catalog.schema.json')
            ->assertOk()
            ->assertHeader('Content-Type', 'application/json; charset=UTF-8')
            ->assertJsonPath('$id', PlaygroundSchema::SCHEMA_ID);
    }

    public function test_component_page_shows_the_node_editor(): void
    {
        $component = PlaygroundCatalog::find('text-input');

        $this->get('/playground/text-input')
            ->assertOk()
            ->assertSee('data-playground-editor="text-input"', false)
            ->assertSee('data-playground-config', false)
            ->assertSee('wire:model.live.debounce.400ms="config"', false)
            ->assertSee(PlaygroundSchema::encodeNode($component))
            ->assertSee('placeholder="Enter a title"', false)
            ->assertDontSee('data-playground-errors', false);
    }

    public function test_edited_json_rerenders_and_invalid_json_stays_inline(): void
    {
        $component = PlaygroundCatalog::find('text-input');
        $rendered = PlaygroundPreview::render($component);
        $node = json_decode(PlaygroundSchema::encodeNode($component), true, 512, JSON_THROW_ON_ERROR);

        $edited = $node;
        $edited['defaults']['placeholder'] = 'Edited in the playground';

        $invalid = $node;
        $invalid['defaults']['placeholder'] = 'Should not render';
        $invalid['defaults']['notARealKey'] = 'nope';

        $editor = Livewire::test(PlaygroundConfig::class, [
            'componentId' => 'text-input',
            'preview' => $rendered['html'],
            'gap' => null,
        ])
            ->assertSet('preview', $rendered['html'])
            ->assertSet('schemaErrors', [])
            ->set('config', json_encode($edited, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR))
            ->assertSet('schemaErrors', [])
            ->assertDontSee('data-playground-errors', false)
            ->set('config', '{')
            ->assertSee('data-playground-errors', false)
            ->assertSee('Invalid JSON', false)
            ->set('config', json_encode($invalid, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR))
            ->assertSee('data-playground-errors', false)
            ->assertSee('notARealKey', false);

        $this->assertStringContainsString('placeholder="Edited in the playground"', (string) $editor->instance()->preview);
        $this->assertStringNotContainsString('placeholder="Should not render"', (string) $editor->instance()->preview);
    }
}
