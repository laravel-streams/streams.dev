<?php

namespace Tests\Feature;

use App\Support\PlaygroundCatalog;
use App\Support\PlaygroundPreview;
use DOMDocument;
use DOMXPath;
use Illuminate\Log\Events\MessageLogged;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class PlaygroundComponentTest extends TestCase
{
    public function test_every_catalog_component_renders_with_its_defaults(): void
    {
        $catalog = json_decode(file_get_contents(resource_path('playground/catalog.json')), true, 512, JSON_THROW_ON_ERROR);
        $this->assertNotEmpty($catalog['components']);

        $gaps = [];
        Log::listen(function (MessageLogged $event) use (&$gaps): void {
            if ($event->message === 'Playground render gap.') {
                $gaps[] = $event->context;
            }
        });

        foreach ($catalog['components'] as $component) {
            $response = $this->get('/playground/'.$component['id']);
            $response->assertOk();
            $response->assertViewIs('playground.component');

            $html = $response->getContent();
            $this->assertStringNotContainsString('data-playground-gap', $html, $component['id']);
            $this->assertStringContainsString('href="'.$component['reference'].'"', $html, $component['id']);

            $preview = $this->preview($html, $component['id']);
            $this->assertNotNull($preview, $component['id']);
            $this->assertTrue($preview->hasChildNodes(), $component['id']);
            $this->assertNotSame('', trim($response->viewData('preview')), $component['id']);
            $this->assertSame([], $gaps, $component['id'].' logged a render exception.');
        }
    }

    public function test_default_controls_and_native_package_views_are_present(): void
    {
        $this->get('/playground/text-input')->assertOk()
            ->assertSee('placeholder="Enter a title"', false)
            ->assertSee('wire:model="data.title"', false);
        $this->get('/playground/select-input')->assertOk()
            ->assertSee('value="draft"', false)
            ->assertSee('value="published"', false);
        $this->get('/playground/table')->assertOk()->assertSee('Ada Lovelace');
        $this->get('/playground/icon-column')->assertOk()->assertSee('<svg', false);
        $this->get('/playground/editor-input')->assertOk()
            ->assertSee('monaco.editor.create', false)
            ->assertSee('data.content-editor', false)
            ->assertSee('wire:snapshot', false);
    }

    public function test_unknown_component_is_not_found(): void
    {
        $this->get('/playground/not-a-component')->assertNotFound();
    }

    public function test_a_failed_render_is_logged_as_a_gap(): void
    {
        $component = PlaygroundCatalog::find('text-input');
        $this->assertNotNull($component);

        $logged = [];
        Log::listen(function (MessageLogged $event) use (&$logged): void {
            $logged[] = $event;
        });

        $rendered = PlaygroundPreview::render([
            ...$component,
            'defaults' => [
                ...$component['defaults'],
                'view' => 'playground.previews.missing',
            ],
        ]);

        $this->assertNull($rendered['html']);
        $this->assertSame('text-input', $rendered['gap']);
        $gaps = collect($logged)->filter(
            fn (MessageLogged $event): bool => $event->level === 'warning'
                && $event->message === 'Playground render gap.'
                && ($event->context['component'] ?? null) === 'text-input'
        );
        $this->assertCount(1, $gaps);
        $this->assertSame(\InvalidArgumentException::class, $gaps->first()->context['exception']);
        $this->assertStringContainsString('playground.previews.missing', $gaps->first()->context['message']);

        $this->view('playground.component', [
            'components' => PlaygroundCatalog::components(),
            'component' => $component,
            'active' => 'text-input',
            'preview' => $rendered['html'],
            'gap' => $rendered['gap'],
        ])->assertSee('data-playground-gap="text-input"', false)
            ->assertSee('logged as a playground gap', false)
            ->assertDontSee('playground.previews.missing', false)
            ->assertDontSee('data-playground-preview', false);

        // A failed component must not poison subsequent renders.
        $this->get('/playground/text-input')->assertOk()
            ->assertSee('data-playground-preview="text-input"', false)
            ->assertDontSee('data-playground-gap', false);
    }

    private function preview(string $html, string $id): ?\DOMNode
    {
        $document = new DOMDocument;
        $previous = libxml_use_internal_errors(true);

        try {
            $document->loadHTML($html);
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($previous);
        }

        $matches = (new DOMXPath($document))->query('//*[@data-playground-preview="'.$id.'"]');

        return $matches->item(0);
    }
}
