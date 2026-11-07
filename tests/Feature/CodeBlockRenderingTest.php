<?php

namespace Tests\Feature;

use App\Livewire\PlaygroundConfig;
use App\Support\PlaygroundCatalog;
use App\Support\PlaygroundPreview;
use App\Support\PlaygroundSchema;
use Livewire\Livewire;
use Tests\TestCase;

class CodeBlockRenderingTest extends TestCase
{
    public function test_docs_php_code_block_renders_prism_panel(): void
    {
        $this->get('/docs/ui/form-layouts')
            ->assertOk()
            ->assertSee('class="st-code"', false)
            ->assertSee('st-code__chrome', false)
            ->assertSee('language-php', false)
            ->assertSee('example.php', false);
    }

    public function test_ui_playground_guide_wraps_json_and_shell_samples(): void
    {
        $html = $this->get('/docs/ui-playground')
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('language-json', $html);
        $this->assertStringContainsString('language-bash', $html);
        $this->assertStringContainsString('st-code__chrome', $html);
        $this->assertGreaterThan(1, substr_count($html, 'class="st-code"'));
    }

    public function test_playground_editor_exposes_json_prism_markup(): void
    {
        $this->get('/playground/text-input')
            ->assertOk()
            ->assertSee('data-playground-config-highlight', false)
            ->assertSee('wire:ignore', false)
            ->assertSee('language-json', false)
            ->assertSee('st-code', false);
    }

    public function test_playground_livewire_round_trip_keeps_highlight_layer(): void
    {
        $component = PlaygroundCatalog::find('text-input');
        $rendered = PlaygroundPreview::render($component);
        $node = json_decode(PlaygroundSchema::encodeNode($component), true, 512, JSON_THROW_ON_ERROR);
        $node['defaults']['placeholder'] = 'Prism after Livewire';

        Livewire::test(PlaygroundConfig::class, [
            'componentId' => 'text-input',
            'preview' => $rendered['html'],
            'gap' => null,
        ])
            ->set('config', json_encode($node, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR))
            ->assertSee('wire:ignore', false)
            ->assertSee('data-playground-config-highlight', false)
            ->assertSee('Prism after Livewire', false);
    }
}
